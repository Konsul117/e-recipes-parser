<?php

namespace common\modules\recipes\models\parsing;

use yii\base\Model;
use yii\validators\NumberValidator;
use yii\validators\RequiredValidator;
use yii\validators\StringValidator;
use yiiCustom\validators\FilterClearTextValidator;

/**
 * Модель ароматизатора.
 */
class FlavorModel extends Model {

	/** @var string Идентификатор */
	public $id;
	const ATTR_ID = 'id';

	/** @var string Название */
	public $title;
	const ATTR_TITLE = 'title';

	/** @var string Название бренда */
	public $brandTitle;
	const ATTR_BRAND_TITLE = 'brandTitle';

	/** @var string Идентификатор бренда */
	public $brandId;
	const ATTR_BRAND_ID = 'brandId';

	/** @var float Процент содержания */
	public $content;
	const ATTR_CONTENT = 'content';

	/**
	 * @inheritdoc
	 */
	public function rules() {
		return [
			[static::ATTR_ID,          RequiredValidator::class],
			[static::ATTR_ID,          FilterClearTextValidator::class],
			[static::ATTR_TITLE,       RequiredValidator::class],
			[static::ATTR_TITLE,       StringValidator::class],
			[static::ATTR_TITLE,       FilterClearTextValidator::class],
			[static::ATTR_BRAND_ID,    FilterClearTextValidator::class],
			[static::ATTR_BRAND_TITLE, StringValidator::class],
			[static::ATTR_BRAND_TITLE, FilterClearTextValidator::class],
			[static::ATTR_CONTENT,     RequiredValidator::class],
			[static::ATTR_CONTENT,     NumberValidator::class, 'min' => 0, 'max' => 1],
		];
	}
}