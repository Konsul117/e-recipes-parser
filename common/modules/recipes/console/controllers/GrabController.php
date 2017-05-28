<?php

namespace common\modules\recipes\console\controllers;

use common\modules\recipes\components\crawlers\AbstractCrawler;
use common\modules\recipes\components\crawlers\ELiquidRecipesCrawler;
use common\modules\recipes\components\crawlers\VapeCraftCrawler;
use common\modules\recipes\components\downloadProvider\HttpDownloadProvider;
use common\modules\recipes\components\downloadProvider\LoadedPage;
use common\modules\recipes\components\parsers\eLiquidRecipes\ELiquidRecipeParser;
use common\modules\recipes\components\parsers\RecipePageParserInterface;
use common\modules\recipes\components\parsers\vapeCraft\VapeCraftRecipePageParser;
use common\modules\recipes\components\RecipesLogger;
use common\modules\recipes\components\RecipesSaver;
use common\modules\recipes\models\parsing\RecipesPageModel;
use common\modules\recipes\models\Source;
use Yii;
use yii\console\Controller;

declare(ticks=1);

/**
 * Контроллер граббинга.
 */
class GrabController extends Controller {

	/** @var int Идентификатор источника */
	protected $sourceId;

	/** @var resource Ссылка на ресурс файла блокировки */
	protected $lockFileResource;

	/**
	 * @inheritdoc
	 */
	public function beforeAction($action) {
		if (parent::beforeAction($action) === false) {
			return false;
		}

		if ($this->isRunning() === true) {
			$this->stdout('Процесс уже выполняется');

			return false;
		}

		pcntl_signal(SIGTERM, [$this, 'onSignal']);

		return true;
	}

	/**
	 * Обработчик сигналов.
	 *
	 * @param int $sigNo Идентификатор сигнала
	 */
	public function onSignal($sigNo) {
		$this->stdout('Процесс прерван по сигналу: ' . $sigNo);

		die();
	}

	/**
	 * @inheritdoc
	 */
	public function afterAction($action, $result) {
		$this->clearLastPageNumber();

		return parent::afterAction($action, $result);
	}

	/**
	 * Начать граббинг e-liquid-recipes.
	 *
	 * @param int  $sourceId Идентификатор источника
	 * @param bool $isResume Нужно ли возобновить предыдущую сессию (начать с последней обработанной страницы)
	 */
	public function actionIndex($sourceId, $isResume = true) {
		$sourceId = (int) $sourceId;
		$this->sourceId = $sourceId;

		if ($this->isRunning() === true) {
			$this->stdout('Процесс уже выполняется');

			return;
		}

		Yii::$app->moduleManager->modules->recipes->currentSourceId = $sourceId;
		RecipesLogger::add('Начинаем выгрузку');
		$isResume = (bool)$isResume;

		$startPageNumber = 1;
		if ($isResume) {
			$lastPageNumber = $this->loadLastPageNumber();

			if ($lastPageNumber !== null) {
				$startPageNumber = $lastPageNumber;
				RecipesLogger::add('Возобновляем работу парсера со страницы: ' . $startPageNumber);
			}
		}

		$downloader = new HttpDownloadProvider();

		$recipeParser = null;/** @var RecipePageParserInterface $recipeParser */
		$crawler = null;/** @var AbstractCrawler $crawler */
		switch($sourceId) {
			case Source::E_LIQUID_RECIPES_ID:
				$recipeParser = new ELiquidRecipeParser();
				$crawler = new ELiquidRecipesCrawler($downloader, $startPageNumber);
				break;

			case Source::VAPE_CRAFT_ID:
				$recipeParser = new VapeCraftRecipePageParser();
				$crawler = new VapeCraftCrawler($downloader, $startPageNumber);
				break;

			default:
				RecipesLogger::add('Неизвестный идентификатор источника: ' . $sourceId);
				return;
		}

		$saver = new RecipesSaver($sourceId);

		$result = $crawler->onRecipePage(function(LoadedPage $page) use ($recipeParser, $saver) {
//			$this->stdout('Страница рецепта: ' . $page->url . PHP_EOL);

			$recipe = $recipeParser->parse($page);

			if ($recipe->isSuccess === false) {
//				$this->stdout('Распарсить рецепт не удалось, URL: ' . $page->url . PHP_EOL);

				return;
			}

			if ($saver->save($recipe) === false) {
//				$this->stdout('Сохранить рецепт не удалось, название: ' . $recipe->title . ', URL: ' . $page->url . PHP_EOL);

				return;
			}
		})
		->onRecipesListPage(function(LoadedPage $page, RecipesPageModel $pageModel) {
			$this->saveLastPageNumber($pageModel->currentPageNumber);
//			$this->stdout('Страница списка рецептов №' . $pageModel->currentPageNumber . ': ' . $page->url . PHP_EOL);
		})
		->start();

		RecipesLogger::add('Выгрузка завершена: ' . ($result ? 'успешно' : 'неуспешно'));
		$this->endRunning();
		$this->clearLastPageNumber();
	}

	/**
	 * Получение номера последней обрабатываемой страницы.
	 *
	 * @return int|null Если null, то данные отсутствуют
	 */
	protected function loadLastPageNumber() {
		$cacheKey = $this->getLastPageNumberCacheKey();

		$result = Yii::$app->cache->get($cacheKey);

		if ($result === false) {
			return null;
		}

		return $result;
	}

	/**
	 * Сохранение номера последней обрабатываемой страницы.
	 *
	 * @param int $number Номер страницы
	 */
	protected function saveLastPageNumber($number) {
		$cacheKey = $this->getLastPageNumberCacheKey();

		Yii::$app->cache->set($cacheKey, $number);
	}

	/**
	 * Очистка сохранённого значения последней обрабатываемой страницы.
	 */
	protected function clearLastPageNumber() {
		$cacheKey = $this->getLastPageNumberCacheKey();

		Yii::$app->cache->delete($cacheKey);
	}

	/**
	 * Получение ключа кэша для номера последней обрабатываемой страницы.
	 *
	 * @return string
	 */
	protected function getLastPageNumberCacheKey() {
		return __METHOD__ . $this->sourceId;
	}

	/**
	 * Проверка, выполняетися ли граббинг для текущего источника в другом процессе.
	 * Если не выполняется, то осуществляется блокировка для текущего процесса.
	 *
	 * @return bool
	 */
	protected function isRunning() {
		$lockFilePath = Yii::getAlias('@runtime');

		if (file_exists($lockFilePath) === false) {
			mkdir($lockFilePath, 0777, true);
		}

		$lockFileDir = $lockFilePath . DIRECTORY_SEPARATOR . 'lock_' . $this->sourceId;

		$this->lockFileResource = fopen($lockFileDir, "w+");

		return (flock($this->lockFileResource, LOCK_EX | LOCK_NB) === false);
	}

	/**
	 * Разблокировка по окончанию процесса.
	 */
	protected function endRunning() {
		if ($this->lockFileResource !== null) {
			fclose($this->lockFileResource);
		}
	}
}