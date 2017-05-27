<?php

namespace common\modules\recipes\components;

use common\modules\recipes\models\Flavor;
use common\modules\recipes\models\FlavorBrand;
use common\modules\recipes\models\FlavorSourceLink;
use common\modules\recipes\models\parsing\FlavorModel;
use common\modules\recipes\models\parsing\RecipeModel;
use common\modules\recipes\models\Recipe;
use common\modules\recipes\models\RecipeFlavor;
use yii\base\ErrorException;

/**
 * Компонент сохранения спарсенных моделей рецептов.
 */
class RecipesSaver {

	/** @var int Идентификатор сайта-источника */
	protected $sourceId;

	/** @var string[] Бренды ароматизаторов: имя бренда => id */
	protected $flavorsBrandsIdsInKeys = [];

	/**
	 * @param int $sourceId Идентификатор сайта-источника
	 */
	public function __construct($sourceId) {
		$this->sourceId = $sourceId;
	}

	/**
	 * Сохранение рецепта.
	 *
	 * @param RecipeModel $inputRecipe Сохраняемый рецепт
	 *
	 * @return bool Успешность сохранения
	 */
	public function save(RecipeModel $inputRecipe) {
		RecipesLogger::add('Начинаем сохранять рецепт id = ' . $inputRecipe->id);
		$recipe = Recipe::findOne([
			Recipe::ATTR_SOURCE_ID        => $this->sourceId,
			Recipe::ATTR_SOURCE_RECIPE_ID => $inputRecipe->id,
		]);/** @var Recipe $inputRecipe */

		if ($recipe === null) {
			RecipesLogger::add('Рецепт в базе отсутствует, создаём');

			$recipe = new Recipe();

			$recipe->source_recipe_id = $inputRecipe->id;
			$recipe->source_id        = $this->sourceId;
		}
		else {
			RecipesLogger::add('Рецепт уже есть, обновляем');
		}

		$recipe->title = $inputRecipe->title;
		$recipe->notes = $inputRecipe->notes;

		if ($recipe->save() === false) {
			RecipesLogger::add('Сохранение рецепта выполнено неуспешно: ' . var_export($recipe->errors, true));

			return false;
		}

		RecipesLogger::add('Обрабатываем ароматизаторы');

		//сначала проверяем на дубли ароматизаторов
		$inputFlavors = [];/** @var FlavorModel[] $inputFlavors */
		foreach ($inputRecipe->flavors as $flavor) {
			$inputFlavors[$flavor->id] = $flavor;
		}

		//если рецепт уже был в базе, то загружаем все связки с аромами
		$savedFlavorsBySourcesIds = [];/** @var RecipeFlavor[] $savedFlavorsBySourcesIds Модели ссылок на сохранённые аромы, индексированные по идентификаторам источника */

		if (false === $recipe->isNewRecord) {
			foreach ($recipe->flavorLinks as $link) {
				foreach ($link->flavor->sourceLinks as $sourceLink) {
					if ($sourceLink->source_id === $this->sourceId) {
						$savedFlavorsBySourcesIds[$sourceLink->source_flavor_id] = $link;

						break;
					}
				}
			}
		}

		foreach ($inputFlavors as $inputFlavor) {
			//если арома уже сохранена, то обновляем её
			if (array_key_exists($inputFlavor->id, $savedFlavorsBySourcesIds) === true) {
				RecipesLogger::add('Обновляем арому "' . $inputFlavor->title . '" (source id = ' . $inputFlavor->id . ')');
				$link = $savedFlavorsBySourcesIds[$inputFlavor->id];

				$link->content = $inputFlavor->content;
			}
			else {
				RecipesLogger::add('Добавляем арому "' . $inputFlavor->title . '" (source id = ' . $inputFlavor->id . ')');

				$link = new RecipeFlavor();

				$link->recipe_id = $recipe->id;
				$link->flavor_id = $this->loadOrAddFlavorBySource($inputFlavor);
			}

			$link->content = $inputFlavor->content;

			if ($link->save() === false) {
				RecipesLogger::add('Сохранение связи рецепта с аромой выполнено неуспешно: ' . var_export($link->errors, true));

				return false;
			}
		}

		//удаляем аромы, которые должны исчезли у рецепта-источника
		$flavorsToDeleteSourceIds = array_diff_key($savedFlavorsBySourcesIds, $inputFlavors);//идентификаторы рецептов по базе источника

		if (count($flavorsToDeleteSourceIds) > 0) {
			//переводим идентификаторы аром в систему сайта
			$flavorsToDeleteSiteIds = FlavorSourceLink::find()
				->select([FlavorSourceLink::ATTR_FLAVOR_ID])
				->where([
					FlavorSourceLink::ATTR_SOURCE_ID        => $this->sourceId,
					FlavorSourceLink::ATTR_SOURCE_FLAVOR_ID => $flavorsToDeleteSourceIds,
				])
				->column();

			RecipeFlavor::deleteAll([
				RecipeFlavor::ATTR_RECIPE_ID => $recipe->id,
				RecipeFlavor::ATTR_FLAVOR_ID => $flavorsToDeleteSiteIds,
			]);
		}

		return true;
	}

	/**
	 * Загрузка или создание аромы в базе по модели спарсенной аромы и возврат идентификатора аромы в базе.
	 *
	 * @param FlavorModel $inputFlavor Спарсенная арома
	 *
	 * @return int
	 *
	 * @throws ErrorException
	 */
	protected function loadOrAddFlavorBySource(FlavorModel $inputFlavor) {
		$flavorId = FlavorSourceLink::find()
			->where([
				FlavorSourceLink::ATTR_SOURCE_ID        => $this->sourceId,
				FlavorSourceLink::ATTR_SOURCE_FLAVOR_ID => $inputFlavor->id,
			])
			->scalar();

		if (false === $flavorId) {
			RecipesLogger::add('Арома ' . $inputFlavor->title . ' (source id = ' . $inputFlavor->id . ') не найдена в базе, добавляем');

			$flavor = new Flavor();

			$flavor->title = $inputFlavor->title;

			$flavor->brand_id = $this->loadOrAddFlavorBrand($inputFlavor->brandTitle);

			if ($flavor->save() === false) {
				RecipesLogger::add('Сохранение аромы не выполнено: ' . var_export($flavor->errors, true));

				throw new ErrorException('Ошибка при сохранении аромы');
			}

			$flavorId = $flavor->id;

			$link = new FlavorSourceLink();

			$link->flavor_id        = $flavorId;
			$link->source_id        = $this->sourceId;
			$link->source_flavor_id = $inputFlavor->id;

			if ($link->save() === false) {
				RecipesLogger::add('Ошибка при сохранении связки ароматизатора с источником: ' . print_r($link->errors, true));

				return false;
			}
		}

		return $flavorId;
	}

	/**
	 * загрузка или добавление бренда аромы по названию бренда и возврат идентификатора.
	 *
	 * @param string $brandTitle
	 *
	 * @return int|null Идентификатор бренда или null, если бренд сохранить невозможно
	 *
	 * @throws ErrorException
	 */
	protected function loadOrAddFlavorBrand($brandTitle) {
		if (array_key_exists($brandTitle, $this->flavorsBrandsIdsInKeys) === false) {
			$brand = new FlavorBrand();

			$brand->title = $brandTitle;

			if ($brand->validate() === false) {
				RecipesLogger::add('Сохранение бренда невозможно, ошибки: ' . var_export($brand->errors, true));

				return null;
			}

			if ($brand->save() === false) {
				RecipesLogger::add('Сохранение бренда не выполнено: ' . var_export($brand->errors, true));

				throw new ErrorException('Ошибка при сохранении бренда');
			}

			$this->flavorsBrandsIdsInKeys[$brandTitle] = $brand->id;
		}

		return $this->flavorsBrandsIdsInKeys[$brandTitle];
	}
}