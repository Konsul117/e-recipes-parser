<?php

namespace common\modules\recipes\components\parsers;

use common\modules\recipes\components\downloadProvider\LoadedPage;
use common\modules\recipes\models\parsing\RecipeModel;

/**
 * Интерфейс парсера страницы рецепта.
 */
interface RecipePageParserInterface {

	/**
	 * Парсинг.
	 *
	 * @param LoadedPage $page Страница
	 *
	 * @return RecipeModel
	 */
	public function parse(LoadedPage $page);

}