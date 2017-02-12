<?php

namespace common\modules\recipes\models;

use yiiCustom\base\ActiveRecord;

/**
 * Ароматизаторы рецептов.
 *
 * @property int $recipe_id Идентификатор рецепта
 * @property int $flavor_id Идентификатор ароматизатора
 */
class RecipeFlavor extends ActiveRecord {

	const ATTR_RECIPE_ID = 'recipe_id';
	const ATTR_FLAVOR_ID = 'flavor_id';

}