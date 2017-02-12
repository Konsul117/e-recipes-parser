<?php

namespace common\modules\user\frontend\models;

use common\modules\user\models\RefUser;
use Yii;
use yii\base\Model;
use yii\validators\BooleanValidator;
use yii\validators\RequiredValidator;

/**
 * Модель формы логина.
 */
class LoginForm extends Model {

	/** @var string Имя пользователя */
	public $username;
	const ATTR_USERNAME = 'username';

	/** @var string Пароль */
	public $password;
	const ATTR_PASSWORD = 'password';

	/** @var string Запомнить меня */
	public $rememberMe = true;
	const ATTR_REMEMBER_ME = 'rememberMe';
	
	/** @var RefUser Модель пользователя */
	protected $_user;

	/**
	 * @inheritdoc
	 */
	public function attributeLabels() {
		return [
			static::ATTR_USERNAME    => 'Имя',
			static::ATTR_PASSWORD    => 'Пароль',
			static::ATTR_REMEMBER_ME => 'Запомнить меня',
		];
	}

	/**
	 * @inheritdoc
	 */
	public function rules() {
		return [
			[static::ATTR_USERNAME, RequiredValidator::class],
			[static::ATTR_PASSWORD, RequiredValidator::class],
			[static::ATTR_REMEMBER_ME, BooleanValidator::class],
			[static::ATTR_PASSWORD, 'validatePassword'],
		];
	}

	/**
	 * Проверка пароля.
	 *
	 * @param string $attribute Аттрибут
	 * @param array  $params    Параметры
	 */
	public function validatePassword($attribute, $params) {
		if (!$this->hasErrors()) {
			$user = $this->getUser();
			if (!$user || !$user->validatePassword($this->password)) {
				$this->addError($attribute, 'Неверное имя ползователя или пароль');
			}
		}
	}

	/**
	 * Вход в систему под данными юзера в модели.
	 *
	 * @return boolean Успешность входа
	 */
	public function login() {
		if ($this->validate()) {
			return Yii::$app->user->login($this->getUser(), $this->rememberMe ? 3600 * 24 * 30 : 0);
		}
		else {
			return false;
		}
	}

	/**
	 * Поиск пользователя по имени.
	 *
	 * @return RefUser|null
	 */
	protected function getUser() {
		if ($this->_user === null) {
			$this->_user = RefUser::findOne([RefUser::ATTR_USERNAME => $this->username]);
		}

		return $this->_user;
	}

}