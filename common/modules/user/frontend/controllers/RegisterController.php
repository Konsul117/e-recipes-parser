<?php

namespace common\modules\user\frontend\controllers;

use common\modules\user\frontend\models\RegisterForm;
use common\widgets\Alert;
use Yii;
use yii\web\Response;
use yiiCustom\core\WebController;

/**
 * Контроллер регистрации пользователя.
 */
class RegisterController extends WebController {

	const ACTION_INDEX = 'index';

	public $title      = 'Авторизация';
	public $mainAction = self::ACTION_INDEX;

	/**
	 * Страница регистрации.
	 *
	 * @return string|Response
	 */
	public function actionIndex() {
		if (Yii::$app->user->isGuest === false) {
			return $this->goHome();
		}

		$registerForm = new RegisterForm();

		if (Yii::$app->request->isPost) {
			if ($registerForm->load(Yii::$app->request->post())) {
				$registeredUser = $registerForm->register();
				
				if ($registeredUser !== false) {
					Yii::$app->user->login($registeredUser);
					
					Yii::$app->session->addFlash(Alert::TYPE_SUCCESS, 'Вы успешно зарегистрированы под именем ' . $registeredUser->username);
					
					return $this->goHome();
				}
			}

			Yii::$app->session->addFlash(Alert::TYPE_ERROR, 'Ошибка при регистрации');
		}

		return $this->render($this->action->id, [
			'registerForm' => $registerForm,
		]);
	}

}