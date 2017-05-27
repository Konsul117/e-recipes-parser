<?php

namespace common\modules\recipes\models;

use yii\db\ActiveQuery;
use yiiCustom\base\ActiveRecord;

/**
 * Рецепт.
 *
 * @property int    $id               Уникальный идентификатор
 * @property string $source_recipe_id Идентификатор рецепта в системе источника
 * @property int    $source_id        Идентификатор источника, с которого взят рецепт
 * @property string $title            Название
 * @property string $notes            Заметка
 *
 * @property-read RecipeFlavor[] $flavorLinks Ссылки на ароматизаторы
 */
class Recipe extends ActiveRecord {

	const ATTR_ID               = 'id';
	const ATTR_SOURCE_RECIPE_ID = 'source_recipe_id';
	const ATTR_SOURCE_ID        = 'source_id';
	const ATTR_TITLE            = 'title';
	const ATTR_NOTES            = 'notes';

	/**
	 * @return ActiveQuery
	 */
	public function getFlavorLinks() {
		return $this->hasMany(RecipeFlavor::class, [RecipeFlavor::ATTR_RECIPE_ID => static::ATTR_ID])
			->with(RecipeFlavor::REL_FLAVOR);
	}
	const REL_FLAVOR_LINKS = 'flavorLinks';

}