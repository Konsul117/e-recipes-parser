<?php

namespace common\modules\recipes\frontend\models\recipes;

use yii\base\Model;
use yii\validators\DefaultValueValidator;
use yii\validators\EachValidator;
use yii\validators\NumberValidator;
use yii\validators\RangeValidator;
use yiiCustom\validators\ReferenceValidator;

/**
 * Модель запроса поиска рецептов.
 */
class RecipesSearchRequest extends Model {

	//Константы типов поиска по ароматизаторам
	/** Тип поиска - поиск рецептов со всеми выбранными ароматизаторами */
	const FLAVORS_FILTER_TYPE_ALL_ID = 1;
	/** Тип поиска - поиск рецептов с любым из выбранных ароматизаторов */
	const FLAVORS_FILTER_TYPE_ANY_ID = 2;

	/** @var int[] Идентификаторы ароматизаторов для фильтрации */
	public $flavorsIds;
	const ATTR_FLAVORS_IDS = 'flavorsIds';

	/** @var int Идентификатор типа поиска */
	public $flavorsFilterTypeId;
	const ATTR_FLAVORS_FILTER_TYPE_ID = 'flavorsFilterTypeId';

	/** @var int Лимит выдачи результатов */
	public $limit;
	const ATTR_LIMIT = 'limit';

	/**
	 * @inheritdoc
	 */
	public function rules() {
		return [
			[static::ATTR_FLAVORS_IDS,            EachValidator::class, 'rule' => [ReferenceValidator::class]],
			[static::ATTR_LIMIT,                  NumberValidator::class, 'max' => 1000],
			[static::ATTR_LIMIT,                  DefaultValueValidator::class, 'value' => 100],
			[static::ATTR_FLAVORS_FILTER_TYPE_ID, NumberValidator::class],
			[static::ATTR_FLAVORS_FILTER_TYPE_ID, RangeValidator::class, 'range' => [
				static::FLAVORS_FILTER_TYPE_ALL_ID,
				static::FLAVORS_FILTER_TYPE_ANY_ID,
			]],
			[static::ATTR_FLAVORS_FILTER_TYPE_ID, DefaultValueValidator::class, 'value' => static::FLAVORS_FILTER_TYPE_ALL_ID],
		];
	}
}