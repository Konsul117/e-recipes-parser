<?php

namespace common\modules\recipes\models\parsing;

use yii\base\Model;

/**
 * Модель списка рецептов страницы.
 */
class RecipesPageModel extends Model {

	/** @var RecipeLinkModel[] Ссылки на рецепты */
	public $recipeLinks = [];

	/** @var bool Парсинг выполнен успешно */
	public $isSuccess = false;

	/** @var int Максимальное количество страниц */
	public $maxPagesCount;

	/** @var int Номер текущей страницы */
	public $currentPageNumber;

}