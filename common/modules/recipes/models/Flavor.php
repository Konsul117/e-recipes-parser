<?php

namespace common\modules\recipes\models;

use yii\db\ActiveQuery;
use yii\validators\DefaultValueValidator;
use yii\validators\NumberValidator;
use yii\validators\RequiredValidator;
use yiiCustom\base\ActiveRecord;
use yiiCustom\validators\FilterClearTextValidator;

/**
 * Ароматизатор.
 *
 * @property int    $id          Уникальный идентификатор
 * @property string $title       Название
 * @property int    $brand_id    Идентификатор бренда
 *
 * @property-read FlavorSourceLink[] $sourceLinks Связи между ароматизатором и источниками
 */
class Flavor extends ActiveRecord {

	const ATTR_ID       = 'id';
	const ATTR_TITLE    = 'title';
	const ATTR_BRAND_ID = 'brand_id';

	/**
	 * @inheritdoc
	 */
	public function rules() {
		return [
			[static::ATTR_TITLE,    FilterClearTextValidator::class],
			[static::ATTR_TITLE,    RequiredValidator::class],
			[static::ATTR_BRAND_ID, NumberValidator::class],
			[static::ATTR_BRAND_ID, DefaultValueValidator::class, 'value' => 0],
		];
	}

	/**
	 * @return ActiveQuery
	 */
	public function getSourceLinks() {
		return $this->hasMany(FlavorSourceLink::class, [FlavorSourceLink::ATTR_FLAVOR_ID => static::ATTR_ID]);
	}
	const REL_SOURCE_LINKS = 'sourceLinks';

}