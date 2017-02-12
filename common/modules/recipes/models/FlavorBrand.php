<?php

namespace common\modules\recipes\models;

use yiiCustom\base\ActiveRecord;

/**
 * Бренды ароматизаторов.
 *
 * @property int    $id    Уникальный идентификатор
 * @property string $title Название
 */
class FlavorBrand extends ActiveRecord {

	const ATTR_ID    = 'id';
	const ATTR_TITLE = 'title';
}