<?php

namespace common\modules\recipes\components\parsers\eLiquidRecipes;

use common\modules\recipes\components\downloadProvider\LoadedPage;
use common\modules\recipes\components\parsers\RecipesListPageParserInterface;
use common\modules\recipes\components\RecipesLogger;
use common\modules\recipes\models\parsing\RecipeLinkModel;
use common\modules\recipes\models\parsing\RecipesPageModel;
use phpQuery;
use phpQueryObject;

/**
 * Парсер страницы списка рецептов для сайта e-liquid-recipes.
 */
class ELiquidRecipesListPageParser implements RecipesListPageParserInterface {

	/**
	 * @inheritdoc
	 */
	public function parse(LoadedPage $page) {
		$result = new RecipesPageModel();

		$phpQueryPage = phpQuery::newDocumentHTML($page->body);/** @var phpQueryObject $phpQueryPage */

		RecipesLogger::add('Парсим ссылки рецептов');

		foreach ($phpQueryPage->find('table.recipelist tr') as $tr) {
			$href = phpQuery::pq($tr)->find('td:first a');

			if ($href->count() === 0) {
				continue;
			}

			$link = new RecipeLinkModel();

			$link->url   = $href->attr('href');
			$link->title = $href->text();

			if ($link->validate() === true) {
				$result->recipeLinks[] = $link;
			}
			else {
				RecipesLogger::add('Ошибки при валидации ссылки: ' . var_export($link->errors, true));
			}
		}

		RecipesLogger::add('Получено ссылок: ' . count($result->recipeLinks));

		$result->isSuccess = true;

		//Получаем максимальный номер страницы
		$liList = $phpQueryPage->find('div.pagination li');
		$a = $liList->eq($liList->length - 2)->find('a');
		$number = (int) $a->text();
		$result->maxPagesCount = $number;

		phpQuery::unloadDocuments();

		return $result;
	}
}