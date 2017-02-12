<?php

namespace common\modules\user\frontend\widgets;

use common\modules\user\models\RefUser;
use Yii;
use yii\base\InvalidConfigException;
use yiiCustom\core\Widget;

/**
 * Виджет информации о пользователе.
 */
class UserInfoWidget extends Widget {

	/** @var string Имя пользователя-продавца */
	public $sellerUserName;

	/** @var int Количество сделок продавца */
	public $sellerDealsCount;

	/** @var int Идентификатор пользователя */
	public $userId;
	const ATTR_USER_ID = 'userId';

	/** @var bool Находится ли пользователь в онлайне */
	public $isOnline;

	/** @var RefUser Модель пользователя */
	protected $user;

	/**
	 * @inheritdoc
	 *
	 * @throws InvalidConfigException
	 */
	public function run() {
		if ($this->userId === null) {
			throw new InvalidConfigException('Не указан идентификатор пользователя');
		}

		//если запрошенный юзер соответсвует тому, что авторизован, то берём его модель из компонента
		if ($this->userId === Yii::$app->user->id) {
			$this->user = Yii::$app->user->identity;
		}
		else {
			$this->user = RefUser::findOne($this->userId);
		}

		$this->sellerUserName   = $this->user->username;
		$this->sellerDealsCount = 0;
		$this->isOnline         = $this->user->checkIsOnline();

		return $this->render('user-info', [
			'widget' => $this,
		]);
	}

}