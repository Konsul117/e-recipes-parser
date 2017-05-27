<?php

namespace common\modules\recipes\console\controllers;

use common\modules\recipes\components\crawlers\ELiquidRecipesCrawler;
use common\modules\recipes\components\downloadProvider\HttpDownloadProvider;
use common\modules\recipes\components\downloadProvider\LoadedPage;
use common\modules\recipes\components\parsers\eLiquidRecipes\ELiquidRecipeParser;
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

	/**
	 * Начать граббинг e-liquid-recipes.
	 *
	 * @param bool $isResume Нужно ли возобновить предыдущую сессию (начать с последней обработанной страницы)
	 */
	public function actionELR($isResume = true) {
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
		$crawler = new ELiquidRecipesCrawler($downloader, $startPageNumber);

		$recipeParser = new ELiquidRecipeParser();
		$saver = new RecipesSaver(Source::E_LIQUID_RECIPES_ID);

		$crawler->onRecipePage(function(LoadedPage $page) use ($recipeParser, $saver) {
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
		return __METHOD__;
	}
}