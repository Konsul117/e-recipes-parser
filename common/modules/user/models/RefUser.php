<?php

namespace common\modules\user\models;

use DateTime;
use DateTimeZone;
use Yii;
use yii\validators\BooleanValidator;
use yii\validators\EmailValidator;
use yii\validators\RequiredValidator;
use yii\web\IdentityInterface;
use yiiCustom\base\ActiveRecord;
use yiiCustom\behaviors\BooleanFieldsBehavior;
use yiiCustom\behaviors\TimestampUTCBehavior;
use yiiCustom\validators\PhoneValidator;

/**
 * Справочник пользователей.
 *
 * @property int    $id                  Уникальный идентификатор пользователя
 * @property string $username            Имя пользователя
 * @property string $password            Пароль
 * @property string $email               E-mail
 * @property string $email_is_confirmed  Признак подтверждения e-mail
 * @property string $phone               Номер телефона
 * @property bool   $phone_is_confirmed  Признак подтверждения телефона
 * @property string $auth_key            Ключ безопасности
 * @property string $last_activity_stamp Дата-время последней активности пользователя
 * @property string $created_stamp       Дата-врем создания записи
 * @property string $updated_stamp       Дата-врем обновления записи
 */
class RefUser extends ActiveRecord implements IdentityInterface {

	const ATTR_ID                  = 'id';
	const ATTR_USERNAME            = 'username';
	const ATTR_PASSWORD            = 'password';
	const ATTR_EMAIL               = 'email';
	const ATTR_EMAIL_IS_CONFIRMED  = 'email_is_confirmed';
	const ATTR_PHONE               = 'phone';
	const ATTR_PHONE_IS_CONFIRMED  = 'phone_is_confirmed';
	const ATTR_AUTH_KEY            = 'auth_key';
	const ATTR_LAST_ACTIVITY_STAMP = 'last_activity_stamp';
	const ATTR_CREATED_STAMP       = 'created_stamp';
	const ATTR_UPDATED_STAMP       = 'updated_stamp';

	/**
	 * @inheritdoc
	 */
	public function rules() {
		return [
			[static::ATTR_USERNAME, RequiredValidator::class],
			[static::ATTR_PASSWORD, RequiredValidator::class],
			[static::ATTR_EMAIL, EmailValidator::class],
			[static::ATTR_EMAIL_IS_CONFIRMED, BooleanValidator::class],
			[static::ATTR_PHONE, PhoneValidator::class],
			[static::ATTR_PHONE_IS_CONFIRMED, BooleanValidator::class],
			[static::ATTR_AUTH_KEY, RequiredValidator::class],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function behaviors() {
		return [
			[
				'class'                                         => TimestampUTCBehavior::class,
				TimestampUTCBehavior::ATTR_CREATED_AT_ATTRIBUTE => static::ATTR_CREATED_STAMP,
				TimestampUTCBehavior::ATTR_UPDATED_AT_ATTRIBUTE => static::ATTR_UPDATED_STAMP,
			],
			[
				'class' => BooleanFieldsBehavior::class,
				BooleanFieldsBehavior::ATTR_FIELDS => [static::ATTR_PHONE_IS_CONFIRMED, static::ATTR_EMAIL_IS_CONFIRMED],
			]
		];
	}

	/**
	 * Проверка пароля на корректность.
	 *
	 * @param string $password
	 *
	 * @return bool
	 */
	public function validatePassword($password) {
		return Yii::$app->security->validatePassword($password, $this->password);
	}

	/**
	 * Генерация ключа авторизации
	 */
	public function generateAuthKey() {
		$this->auth_key = Yii::$app->security->generateRandomString();
	}

	/**
	 * Установка пароля
	 *
	 * @param string $password пароль
	 */
	public function setPassword($password) {
		$this->password = Yii::$app->security->generatePasswordHash($password);
	}

	//реализация IdentityInterface

	/**
	 * @inheritdoc
	 */
	public static function findIdentity($id) {
		return static::findOne($id);
	}

	/**
	 * @inheritdoc
	 */
	public static function findIdentityByAccessToken($token, $type = null) {
		// TODO: Implement findIdentityByAccessToken() method.
	}

	/**
	 * @inheritdoc
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * @inheritdoc
	 */
	public function getAuthKey() {
		return $this->auth_key;
	}

	/**
	 * @inheritdoc
	 */
	public function validateAuthKey($authKey) {
		return $this->auth_key === $authKey;
	}

	/**
	 * Проверка, находится ли пользователь в онлайне.
	 *
	 * @return bool
	 */
	public function checkIsOnline() {
		if ($this->last_activity_stamp === null) {
			return false;
		}

		$utcTimeZone  = new DateTimeZone('UTC');
		$dateTimeNow  = new DateTime('now', $utcTimeZone);
		$dataTimeInDb = new DateTime($this->last_activity_stamp, $utcTimeZone);


		return (($dateTimeNow->getTimestamp() - $dataTimeInDb->getTimestamp()) <= Yii::$app->moduleManager->modules->user->onlineStatusSinceLastActivityPeriod);
	}
}