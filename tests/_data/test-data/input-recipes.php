<?php
use common\modules\recipes\models\parsing\FlavorModel;
use common\modules\recipes\models\parsing\RecipeModel;

return [
	[
		RecipeModel::ATTR_ID      => 987,
		RecipeModel::ATTR_TITLE   => 'Тест-рецепт 1',
		RecipeModel::ATTR_NOTES   => 'Описание',
		RecipeModel::ATTR_FLAVORS => [
			[
				FlavorModel::ATTR_ID          => 345,
				FlavorModel::ATTR_TITLE       => 'Яблоко',
				FlavorModel::ATTR_BRAND_TITLE => 'Konsul Flavor',
				FlavorModel::ATTR_CONTENT     => 0.01,
			],
			[
				FlavorModel::ATTR_ID          => 234,
				FlavorModel::ATTR_TITLE       => 'Груша',
				FlavorModel::ATTR_BRAND_TITLE => 'Konsul Flavor',
				FlavorModel::ATTR_CONTENT     => 0.005,
			],
			[
				FlavorModel::ATTR_ID          => 123,
				FlavorModel::ATTR_TITLE       => 'Крем',
				FlavorModel::ATTR_BRAND_TITLE => 'Konsul Flavor',
				FlavorModel::ATTR_CONTENT     => 0.001,
			],
			[
				FlavorModel::ATTR_ID          => 111,
				FlavorModel::ATTR_TITLE       => 'Супер-вкус',
				FlavorModel::ATTR_BRAND_TITLE => 'TPA',
				FlavorModel::ATTR_CONTENT     => 0.002,
			],
			[
				FlavorModel::ATTR_ID          => 112,
				FlavorModel::ATTR_TITLE       => 'Без бренда',
				FlavorModel::ATTR_BRAND_TITLE => '',
				FlavorModel::ATTR_CONTENT     => 0.002,
			],
		],
	],
	[
		RecipeModel::ATTR_ID      => 987,
		RecipeModel::ATTR_TITLE   => '*Strawberry cream - ROG*',
		RecipeModel::ATTR_NOTES   => 'Описание',
		RecipeModel::ATTR_FLAVORS => [
			[
				FlavorModel::ATTR_ID          => 345,
				FlavorModel::ATTR_TITLE       => 'Яблоко',
				FlavorModel::ATTR_BRAND_TITLE => 'Konsul Flavor',
				FlavorModel::ATTR_CONTENT     => 0.01,
			],
		],
	],
];