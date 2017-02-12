<?php

namespace common\modules\user\frontend\controllers;

use common\modules\user\frontend\models\LoginForm;
use common\widgets\Alert;
use Yii;
use yii\web\Response;
use yiiCustom\core\WebController;

/**
 * Контроллер авторизации.
 */
class AuthController extends WebController {

	const ACTION_INDEX  = 'index';
	const ACTION_LOGOUT = 'logout';

	public $title      = 'Авторизация';
	public $mainAction = self::ACTION_INDEX;

	/**
	 * Страница авторизации.
	 * 
	 * @return string|Response
	 */
	public function actionIndex() {
		if (Yii::$app->user->isGuest === false) {
			return $this->goHome();
		}

		$loginForm = new LoginForm();

		if (Yii::$app->request->isPost) {
			if ($loginForm->load(Yii::$app->request->post()) && $loginForm->login()) {
				Yii::$app->session->addFlash(Alert::TYPE_SUCCESS, 'Вы успешно вошли в систему как ' . $loginForm->username);

				return $this->redirect([Yii::$app->user->getReturnUrl('/')]);
			}
			else {
				Yii::$app->session->addFlash(Alert::TYPE_ERROR, 'Неверно введены имя или пароль');
			}

		}

		return $this->render($this->action->id, [
			'loginForm' => $loginForm,
		]);
	}

	/**
	 * Выход.
	 *
	 * @return Response
	 */
	public function actionLogout() {
		if (Yii::$app->user->isGuest === false) {
			if (Yii::$app->user->logout()) {
				Yii::$app->session->addFlash(Alert::TYPE_SUCCESS, 'Вы успешно вышли из системы');
			}
		}

		return $this->goHome();
	}
	
}