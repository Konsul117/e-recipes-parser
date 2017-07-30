<?php

namespace common\modules\recipes\frontend\models\recipes;

/**
 * Модель рецепта - ответ на запрос поиска.
 */
class RecipeItemResponse {

	/** @var int Идентификатор */
	public $id;

	/** @var string Название */
	public $title;
}