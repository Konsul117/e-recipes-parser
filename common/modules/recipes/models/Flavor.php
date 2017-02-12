<?php

namespace common\modules\recipes\models;

use yii\db\ActiveQuery;
use yiiCustom\base\ActiveRecord;

/**
 * Ароматизаторы.
 *
 * @property int    $id          Уникальный идентификатор
 * @property string $title       Название
 * @property int    $brand_id    Идентификатор бренда
 *
 * @property-read FlavorSourceLink[] $sourceLink Связь между ароматизатором и источниками
 */
class Flavor extends ActiveRecord {

	const ATTR_ID    = 'id';
	const ATTR_TITLE = 'title';

	/**
	 * @return ActiveQuery
	 */
	public function getSourceLink() {
		return $this->hasMany(FlavorSourceLink::class, [FlavorSourceLink::ATTR_FLAVOR_ID => static::ATTR_ID]);
	}
	const REL_SOURCE_LINK = 'sourceLink';

}