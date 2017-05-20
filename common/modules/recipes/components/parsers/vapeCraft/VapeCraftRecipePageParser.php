<?php

namespace common\modules\recipes\components\parsers\vapeCraft;

use common\modules\recipes\components\downloadProvider\LoadedPage;
use common\modules\recipes\components\parsers\RecipePageParserInterface;
use common\modules\recipes\components\RecipesLogger;
use common\modules\recipes\models\parsing\FlavorModel;
use common\modules\recipes\models\parsing\RecipeModel;
use phpQuery;
use phpQueryObject;

/**
 * Парсер страницы рецепта для сайта vapecraft.ru.
 */
class VapeCraftRecipePageParser implements RecipePageParserInterface {

	/**
	 * Парсинг.
	 *
	 * @param LoadedPage $page Страница
	 *
	 * @return RecipeModel
	 */
	public function parse(LoadedPage $page) {
		$result = new RecipeModel();

		RecipesLogger::add('Обрабатываем рецепт по ссылке ' . $page->url);

		$id = null;
		if (preg_match('/\/main\/recept\/([0-9]+)/i', $page->url, $urlResult)) {
			$id = $urlResult[1];
		}

		if ($id === null) {
			RecipesLogger::add('Ошибка при парсинге URL рецепта: ' . $page->url);

			$result->isSuccess = false;

			return $result;
		}

		$phpQueryPage = phpQuery::newDocumentHTML($page->body);/** @var phpQueryObject $phpQueryPage */
		$title  = $phpQueryPage->find('#calc-body-id textarea#title')->val();
		$title = trim($title, chr(0xC2).chr(0xA0));
		$notes = $phpQueryPage->find('#calc-digits-frame-elem-id2 .notes-block .alert')->text();

		$result->id    = $id;
		$result->title = $title;
		$result->notes = $notes;

		foreach ($phpQueryPage->find('#calc-digits-frame-elem-id2 .calc-flavors-table .calc-flavors-table-flavor') as $flavorLine) {
			$flavor = new FlavorModel();

			$flavorHref = phpQuery::pq($flavorLine)->find('.calc-flavors-row-name a.fl-link');
			$flavorLink = $flavorHref->attr('href');

			$flavor->title = $flavorHref->find('.name-label')->text();
			$flavor->content = phpQuery::pq($flavorLine)->find('.calc-flavors-row-value input.calc-flavor-input-value')->val() / 100;

			if (preg_match('/\/main\/flavor\/([0-9]+)?/i', $flavorLink, $flavorLinkResult)) {
				$flavor->id = $flavorLinkResult[1];
			}

			if ($flavor->id === null) {
				RecipesLogger::add('Ошибка при парсинге URL ароматизатора: ' . $flavorHref);

				$result->isSuccess = false;

				return $result;
			}

			$flavor->brandTitle = $flavorHref->find('.make-label')->text();

			if ($flavor->validate() === false) {
				RecipesLogger::add('Добавленный ароматизатор невалиден: ' . var_export($flavor->errors, true));

				$result->isSuccess = false;

				return $result;
			}

			$result->flavors[] = $flavor;
		}

		if ($result->validate() === false) {
			RecipesLogger::add('Рецепт невалиден: ' . var_export($result->errors, true));

			$result->isSuccess = false;

			return $result;
		}

		$result->isSuccess = true;

		phpQuery::unloadDocuments();

		return $result;
	}
}