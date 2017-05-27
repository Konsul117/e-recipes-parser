<?php

namespace common\modules\recipes\models;

use yii\db\ActiveQuery;
use yii\validators\NumberValidator;
use yii\validators\RequiredValidator;
use yiiCustom\base\ActiveRecord;
use yiiCustom\validators\ReferenceValidator;

/**
 * Ароматизаторы рецептов.
 *
 * @property int   $recipe_id Идентификатор рецепта
 * @property int   $flavor_id Идентификатор ароматизатора
 * @property float $content   Процент содержания
 *
 * @property-read Flavor $flavor Ароматизатор
 */
class RecipeFlavor extends ActiveRecord {

	const ATTR_RECIPE_ID = 'recipe_id';
	const ATTR_FLAVOR_ID = 'flavor_id';
	const ATTR_CONTENT   = 'content';

	/**
	 * @inheritdoc
	 */
	public function rules() {
		return [
			[static::ATTR_RECIPE_ID, RequiredValidator::class],
			[static::ATTR_RECIPE_ID, ReferenceValidator::class],
			[static::ATTR_FLAVOR_ID, RequiredValidator::class],
			[static::ATTR_FLAVOR_ID, ReferenceValidator::class],
			[static::ATTR_CONTENT,   RequiredValidator::class],
			[static::ATTR_CONTENT,   NumberValidator::class, 'min' => 0],
		];
	}

	/**
	 * @return ActiveQuery
	 */
	public function getFlavor() {
		return $this->hasOne(Flavor::class, [Flavor::ATTR_ID => static::ATTR_FLAVOR_ID])
			->with(Flavor::REL_SOURCE_LINKS);
	}
	const REL_FLAVOR = 'flavor';

}