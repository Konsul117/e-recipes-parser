<?php

namespace common\modules\recipes\frontend\models\recipes;

/**
 * Модель ответа на запрос поиска рецептов.
 */
class RecipesResponse {

	/** @var RecipeItemResponse[] Рецепты */
	public $recipes;

	/** @var int Общее количество найденных рецептов */
	public $totalCount;

}