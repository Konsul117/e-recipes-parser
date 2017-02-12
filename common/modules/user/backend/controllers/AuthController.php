<?php

namespace common\modules\user\backend\controllers;

use common\modules\user\backend\models\LoginForm;
use common\widgets\Alert;
use Yii;
use yii\web\Response;
use yiiCustom\base\BackendController;

class AuthController extends BackendController {

	const ACTION_INDEX       = 'index';
	const PARAM_INDEX_REPEAT = 'repeat';

	const ACTION_LOGOUT    = 'logout';
	const ACTION_NO_ACCESS = 'no-access';
	
	public $layout = '//no-auth';
	
	protected $needAuthorise = false;

	/**
	 * Страница авторизации.
	 *
	 * @param bool $repeat Повторная авторизация
	 *
	 * @return string|Response
	 */
	public function actionIndex($repeat = false) {
		$this->view->title = 'Авторизация';

		//если пользователь уже авторизован
		if (Yii::$app->user->isGuest === false) {
			if ($repeat) {
				Yii::$app->user->logout();
			}
			else {
				return $this->goHome();
			}
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
			Yii::$app->user->logout();
		}

		return $this->redirect(Yii::$app->moduleManager->modules->homeFrontend->getHomeUrl());
	}

	/**
	 * Страница с сообщением об отсутствии доступа к админке.
	 * Сюда пользователь попадает, когда он авторизован, но недостаточно прав для работы с админкой.
	 *
	 * @return string
	 */
	public function actionNoAccess() {
		$this->view->title = 'Отсутствует доступ';

		return $this->render($this->action->id, [

		]);
	}

}