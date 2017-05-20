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

}