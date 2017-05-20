<?php

namespace common\modules\recipes\components\parsers\vapeCraft;

use common\modules\recipes\components\downloadProvider\LoadedPage;
use common\modules\recipes\components\parsers\RecipesListPageParserInterface;
use common\modules\recipes\components\RecipesLogger;
use common\modules\recipes\models\parsing\RecipeLinkModel;
use common\modules\recipes\models\parsing\RecipesPageModel;
use phpQuery;
use phpQueryObject;

/**
 * Парсер страницы списка рецептов для сайта vapecraft.ru.
 */
class VapeCraftRecipesListPageParser implements RecipesListPageParserInterface {

	/**
	 * Парсинг.
	 *
	 * @param LoadedPage $page Страница
	 *
	 * @return RecipesPageModel
	 */
	public function parse(LoadedPage $page) {
		$result = new RecipesPageModel();

		RecipesLogger::add('Парсим ссылки рецептов');

		$phpQueryPage = phpQuery::newDocumentHTML($page->body);/** @var phpQueryObject $phpQueryPage */

		foreach ($phpQueryPage->find('.container .tbl_grid') as $tr) {
			$href = phpQuery::pq($tr)->find('.row.r1 .r_name a');

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

		phpQuery::unloadDocuments();

		RecipesLogger::add('Получено ссылок: ' . count($result->recipeLinks));

		$result->isSuccess = true;

		return $result;
	}
}