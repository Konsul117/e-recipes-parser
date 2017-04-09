<?php

namespace common\modules\recipes\models;

use yii\validators\RequiredValidator;
use yiiCustom\base\ActiveRecord;
use yiiCustom\validators\FilterClearTextValidator;

/**
 * Бренды ароматизаторов.
 *
 * @property int    $id    Уникальный идентификатор
 * @property string $title Название
 */
class FlavorBrand extends ActiveRecord {

	const ATTR_ID    = 'id';
	const ATTR_TITLE = 'title';

	/**
	 * @inheritdoc
	 */
	public function rules() {
		return [
			[static::ATTR_TITLE, RequiredValidator::class],
			[static::ATTR_TITLE, FilterClearTextValidator::class],
		];
	}


}