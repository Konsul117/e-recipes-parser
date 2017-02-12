<?php

namespace common\modules\user\frontend\models;

use common\modules\user\components\AclHelper;
use common\modules\user\models\RefUser;
use Yii;
use yii\base\Model;
use yii\validators\CompareValidator;
use yii\validators\EmailValidator;
use yii\validators\RequiredValidator;
use yii\validators\StringValidator;
use yii\validators\UniqueValidator;

/**
 * Модель формы регистрации.
 */
class RegisterForm extends Model {

	/** @var string Имя пользователя */
	public $username;
	const ATTR_USERNAME = 'username';

	/** @var string Пароль */
	public $password;
	const ATTR_PASSWORD = 'password';

	/** @var string Подтверждение пароля */
	public $passwordRepeat;
	const ATTR_PASSWORD_REPEAT = 'passwordRepeat';

	/** @var string E-mail */
	public $email;
	const ATTR_EMAIL = 'email';

	/**
	 * @inheritdoc
	 */
	public function rules() {
		return [
			[static::ATTR_USERNAME, RequiredValidator::class],
			[static::ATTR_USERNAME, UniqueValidator::class, 'targetClass' => RefUser::class],
			[static::ATTR_PASSWORD, RequiredValidator::class],
			[static::ATTR_PASSWORD, StringValidator::class, 'min' => 6, 'max' => 20, 'tooShort' => 'Пароль должен быть длиной не менее 6 символов', 'tooLong' => 'Пароль должен быть длиной не более 20 символов'],
			[static::ATTR_PASSWORD_REPEAT, RequiredValidator::class],
			[static::ATTR_PASSWORD, CompareValidator::class, 'compareAttribute' => static::ATTR_PASSWORD_REPEAT, 'message' => 'Пароль и подтверждение должны совпадать'],
			[static::ATTR_EMAIL, RequiredValidator::class],
			[static::ATTR_EMAIL, UniqueValidator::class, 'targetClass' => RefUser::class],
			[static::ATTR_EMAIL, EmailValidator::class],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels() {
		return [
			static::ATTR_USERNAME        => 'Имя пользователя',
			static::ATTR_PASSWORD        => 'Пароль',
			static::ATTR_PASSWORD_REPEAT => 'Подтверждение пароля',
			static::ATTR_EMAIL           => 'E-mail',
		];
	}

	/**
	 * Регистрация пользователя
	 *
	 * @return RefUser|bool
	 */
	public function register() {
		if ($this->validate()) {
			$user = new RefUser();
			$user->username	 = $this->username;
			$user->email	 = $this->email;
			$user->setPassword($this->password);
			$user->generateAuthKey();

			//если юзер успешно создан, то автоматом добавляем ему роль покупателя
			if ($user->save()) {
				$auth = Yii::$app->authManager;
				
				$authorRole = $auth->getRole(AclHelper::ROLE_BUYER);
				$auth->assign($authorRole, $user->id);

				return $user;
			}

		}

		return false;
	}

}