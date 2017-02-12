<?php

namespace common\modules\recipes\models;

use yiiCustom\base\ActiveRecord;

/**
 * Типы импортируемых сущностей.
 *
 * @property int    $id    Уникальный идентификатор
 * @property string $title Название типа сущности
 */
class ImportEntityType extends ActiveRecord {

	const ATTR_ID    = 'id';
	const ATTR_TITLE = 'title';

	/** Тип - ароматизатор */
	const FLAVOR_TYPE_ID = 1;

	/** Тип - рецепт */
	const RECIPE_TYPE_ID = 2;
}