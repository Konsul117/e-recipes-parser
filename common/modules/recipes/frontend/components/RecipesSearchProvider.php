<?php

namespace common\modules\recipes\frontend\components;

use common\modules\recipes\frontend\models\recipes\RecipeItemResponse;
use common\modules\recipes\frontend\models\recipes\RecipesResponse;
use common\modules\recipes\frontend\models\recipes\RecipesSearchRequest;
use common\modules\recipes\models\Recipe;
use common\modules\recipes\models\RecipeFlavor;

/**
 * Провайдер поиска рецептов.
 */
class RecipesSearchProvider {

	/** @var RecipesSearchRequest Модель запроса поиска рецептов */
	protected $request;

	/**
	 * @param RecipesSearchRequest $request Модель запроса поиска рецептов
	 */
	public function __construct(RecipesSearchRequest $request) {
		$this->request = $request;
	}

	/**
	 * Выполнение поиска.
	 *
	 * @return RecipesResponse|null Модель ответа с рецептами или null, если возникла ошибка при поиске
	 */
	public function search() {
		$request = $this->request;
		if ($request->validate() === false) {
			return null;
		}

		$result = new RecipesResponse();

		$query = Recipe::find();

		if (is_array($request->flavorsIds)) {
			if ((int)$request->flavorsFilterTypeId === $request::FLAVORS_FILTER_TYPE_ALL_ID) {
				foreach ($request->flavorsIds as $flavorId) {
					$alias = 't_' . $flavorId;
					$query->innerJoin(RecipeFlavor::tableName() . ' as ' . $alias, $alias . '.' . RecipeFlavor::ATTR_RECIPE_ID . ' = ' . Recipe::tableName() . '.' . Recipe::ATTR_ID . ' AND ' . $alias . '.' . RecipeFlavor::ATTR_FLAVOR_ID . '=' . $flavorId);
				}
			}
			elseif ((int)$request->flavorsFilterTypeId === $request::FLAVORS_FILTER_TYPE_ANY_ID) {
				$query->innerJoinWith(Recipe::REL_FLAVOR_LINKS);
				$query->andWhere([
					RecipeFlavor::tableName() . '.' . RecipeFlavor::ATTR_FLAVOR_ID => $request->flavorsIds
				]);
			}
		}

		$result->totalCount = $query->count();

		$query->limit($request->limit);

		$queryResult = $query->all();/** @var Recipe[] $queryResult */

		$result->recipes = [];

		foreach ($queryResult as $recipe) {
			$recipeResponse = new RecipeItemResponse();

			$recipeResponse->id    = $recipe->id;
			$recipeResponse->title = $recipe->title;

			$result->recipes[] = $recipeResponse;
		}

		return $result;
	}
}