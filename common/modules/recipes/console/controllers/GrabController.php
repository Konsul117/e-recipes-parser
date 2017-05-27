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

/**
 * Контроллер граббинга.
 */
class GrabController extends Controller {

	/** @var int Идентификатор источника */
	protected $sourceId;

	/**
	 * Начать граббинг e-liquid-recipes.
	 *
	 * @param int  $sourceId Идентификатор источника
	 * @param bool $isResume Нужно ли возобновить предыдущую сессию (начать с последней обработанной страницы)
	 */
	public function actionIndex($sourceId, $isResume = true) {
		$sourceId = (int) $sourceId;
		RecipesLogger::add('Начинаем выгрузку');
		$this->sourceId = $sourceId;
		Yii::$app->moduleManager->modules->recipes->currentSourceId = $sourceId;
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
	 * Получение ключа кэша для номера последней обрабатываемой страницы.
	 *
	 * @return string
	 */
	protected function getLastPageNumberCacheKey() {
		return __METHOD__ . $this->sourceId;
	}
}