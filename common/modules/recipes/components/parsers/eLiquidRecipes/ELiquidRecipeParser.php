<?php

namespace common\modules\recipes\components\parsers\eLiquidRecipes;

use common\modules\recipes\components\downloadProvider\LoadedPage;
use common\modules\recipes\components\parsers\RecipePageParserInterface;
use common\modules\recipes\components\RecipesLogger;
use common\modules\recipes\models\parsing\FlavorModel;
use common\modules\recipes\models\parsing\RecipeModel;
use phpQuery;
use phpQueryObject;

/**
 * Парсер страницы рецепта для сайта e-liquid-recipes.
 */
class ELiquidRecipeParser implements RecipePageParserInterface {

	/**
	 * @inheritdoc
	 */
	public function parse(LoadedPage $page) {
		$result = new RecipeModel();

		RecipesLogger::add('Обрабатываем рецепт по ссылке ' . $page->url);

		$id = null;
		if (preg_match('/recipe\/([0-9]+)\//i', $page->url, $urlResult)) {
			$id = $urlResult[1];
		}

		if ($id === null) {
			RecipesLogger::add('Ошибка при парсинге URL рецепта: ' . $page->url);

			$result->isSuccess = false;

			return $result;
		}

		$phpQueryPage = phpQuery::newDocumentHTML($page->body);/** @var phpQueryObject $phpQueryPage */
		$title = $phpQueryPage->find('#recipecontent #rname')->text();
		$title = trim($title, chr(0xC2).chr(0xA0));
		$notes = $phpQueryPage->find('#recipecontent #rnotes')->text();

		$result->id    = $id;
		$result->title = $title;
		$result->notes = $notes;

		//обрабатываем все ароматизаторы на странице рецепта
		foreach ($phpQueryPage->find('#recipecontent #recflavor .recline') as $flavorLine) {
			$flavor = new FlavorModel();

			$pqFlavorLine = phpQuery::pq($flavorLine);

			$flavorHref = $pqFlavorLine->find('.rlab a');
			$flavorLink = $flavorHref->attr('href');

			$flavor->title   = $flavorHref->text();
			$flavor->content = $pqFlavorLine->find('.runit')->text() / 100;

			if (preg_match('/flavor\/([0-9]+)\/?/i', $flavorLink, $flavorLinkResult)) {
				$flavor->id = $flavorLinkResult[1];
			}

			if ($flavor->id === null) {
				RecipesLogger::add('Ошибка при парсинге URL ароматизатора: ' . $flavorHref);

				$result->isSuccess = false;

				return $result;
			}

			$flavor->brandTitle = $flavorHref->find('abbr')->attr('title');

			if ($flavor->validate() === false) {
				RecipesLogger::add('Добавленный ароматизатор невалиден: ' . var_export($flavor->errors, true));

				$result->isSuccess = false;

				return $result;
			}

			$result->flavors[] = $flavor;
		}

		if ($result->validate() === false) {
			RecipesLogger::add('Рецепт невалиден: ' . var_export($result->errors, true) . ', аттрибуты: ' . var_export($result->attributes, true));

			$result->isSuccess = false;

			return $result;
		}

		$result->isSuccess = true;

		phpQuery::unloadDocuments();

		return $result;
	}
}