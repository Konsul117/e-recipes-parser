<?php

namespace common\modules\user\models;
use Yii;

/**
 * Модель-обёртка для данных пользователя.
 */
class UserData {

	/** @var int Уникальный идентификатор пользователя */
	public $id;

	/** @var string Имя пользователя */
	public $userName;

	/** @var string E-mail */
	public $email;

	/** @var bool Признак подтверждения e-mail */
	public $emailIsConfirmed;

	/** @var string Номер телефона */
	public $phone;

	/** @var string Признак подтверждения телефона */
	public $phoneIsConfirmed;

	/** @var bool Находится ли пользователь в онлайне */
	public $isOnline;

	/** @var RefUser */
	protected $userModel;

	/** @var bool Cвязан ли пользователь с учётной записью в telegram */
	protected $isLinkedWithTelegram;

	/**
	 * @param RefUser $user
	 */
	public function __construct(RefUser $user) {
		$this->userModel = $user;

		$this->init();
	}

	/**
	 * Проверка, связан ли пользователь с учётной записью в telegram.
	 */
	public function checkUserIsLinkedWithTelegram() {
		if ($this->isLinkedWithTelegram === null) {
			$this->isLinkedWithTelegram = Yii::$app->moduleManager->modules->telegram->checkUserIsLinkedWithTelegram($this->id);
		}

		return $this->isLinkedWithTelegram;
	}

	/**
	 * Инициализация данных модели.
	 */
	protected function init() {
		$this->id               = (int)$this->userModel->id;
		$this->userName         = $this->userModel->username;
		$this->email            = $this->userModel->email;
		$this->emailIsConfirmed = (bool)$this->userModel->email_is_confirmed;
		$this->phone            = $this->userModel->phone;
		$this->phoneIsConfirmed = (bool)$this->userModel->phone_is_confirmed;
		$this->isOnline         = $this->userModel->checkIsOnline();
	}

}