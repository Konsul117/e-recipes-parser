<?php

namespace common\components;

use Yii;
use yii\web\Response;
use yiiCustom\core\WebController;

/**
 * Базовый класс для ajax-контроллеров.
 */
class AjaxController extends WebController {

	public $enableCsrfValidation = false;

	/**
	 * @inheritdoc
	 */
	public function beforeAction($action) {
		if (parent::beforeAction($action) === false) {
			return false;
		}

		if (Yii::$app->request->isAjax === false) {
//			throw new InvalidCallException('Некорректный запрос');
		}

		Yii::$app->response->format = Response::FORMAT_JSON;

		return true;
	}
}