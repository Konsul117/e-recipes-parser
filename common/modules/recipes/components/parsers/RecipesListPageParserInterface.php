<?php

namespace common\modules\recipes\components\parsers;

use common\modules\recipes\components\downloadProvider\LoadedPage;
use common\modules\recipes\models\parsing\RecipesPageModel;

/**
 * Интерфейс парсера страницы списка рецептов.
 */
interface RecipesListPageParserInterface {

	/**
	 * Парсинг.
	 *
	 * @param LoadedPage $page Страница
	 *
	 * @return RecipesPageModel
	 */
	public function parse(LoadedPage $page);

}