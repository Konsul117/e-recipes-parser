<?php

namespace common\modules\recipes\models;

use yiiCustom\base\ActiveRecord;

/**
 * Рецепты.
 *
 * @property int    $id               Уникальный идентификатор
 * @property string $source_recipe_id Идентификатор рецепта в системе источника
 * @property int    $source_id        Идентификатор источника, с которого взят рецепт
 * @property string $title            Название
 * @property string $notes            Заметка
 */
class Recipe extends ActiveRecord {

	const ATTR_ID               = 'id';
	const ATTR_SOURCE_RECIPE_ID = 'source_recipe_id';
	const ATTR_SOURCE_ID        = 'source_id';
	const ATTR_TITLE            = 'title';
	const ATTR_NOTES            = 'notes';

}