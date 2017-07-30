<?php

namespace common\modules\recipes\frontend\controllers;

use common\components\AjaxController;
use common\modules\recipes\frontend\components\RecipesSearchProvider;
use common\modules\recipes\frontend\models\flavorSearch\FlavorsRequest;
use common\modules\recipes\frontend\models\recipes\RecipesSearchRequest;
use common\modules\recipes\models\FlavorBrand;
use common\modules\recipes\models\Source;
use Yii;
use yii\base\InvalidParamException;
use yiiCustom\base\AjaxResponse;

/**
 * Контроллер получения основных данных.
 */
class BaseDataController extends AjaxController {

	/**
	 * Получение ароматизаторов.
	 *
	 * @return AjaxResponse
	 *
	 * @throws InvalidParamException
	 */
	public function actionGetFlavors() {
		$request = new FlavorsRequest();

		$request->load(Yii::$app->request->get(), '');

		$response = new AjaxResponse();

		if ($request->validate() === true) {
			$response->result = true;
			$response->data   = $request->search();

		}
		else {
			throw new InvalidParamException();
		}

		return $response;
	}

	/**
	 * Получение справочников.
	 *
	 * @return AjaxResponse
	 *
	 * @throws InvalidParamException
	 */
	public function actionGetReferences() {
		//todo вынести из контроллера в отдельный провайдер

		$response = new AjaxResponse();

		$response->result = true;

		$response->data = [
			'brands' => FlavorBrand::find()
				->select([FlavorBrand::ATTR_ID, FlavorBrand::ATTR_TITLE])
				->orderBy([FlavorBrand::ATTR_TITLE => SORT_ASC])
				->asArray()
				->all(),
			'sources' => Source::find()
				->select([Source::ATTR_ID, Source::ATTR_TITLE])
				->orderBy([Source::ATTR_TITLE => SORT_ASC])
				->asArray()
				->all(),
		];

		return $response;
	}

	/**
	 * Поиск рецептов.
	 *
	 * @return AjaxResponse
	 */
	public function actionFindRecipes() {
		$response = new AjaxResponse();

		$request = new RecipesSearchRequest();

		$request->load(Yii::$app->request->get(), '');

		$provider = new RecipesSearchProvider($request);

		$result = $provider->search();

		$response->result = ($result !== null);

		if ($result !== null) {
			$response->data = $result;
		}

		return $response;
	}
}