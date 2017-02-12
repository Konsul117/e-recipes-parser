<?php

namespace common\modules\user\components;

use common\modules\user\models\RefUser;
use DateTime;
use DateTimeZone;
use Yii;
use yii\base\Behavior;
use yii\base\Controller;
use yiiCustom\helpers\DateHelper;

/**
 * Поведение для состояния авторизации пользователя.
 */
class AuthBehavior extends Behavior {

	/**
	 * @inheritdoc
	 */
	public function events() {
		return [
			Controller::EVENT_BEFORE_ACTION => function() {
				if ((Yii::$app->user !== null) && (Yii::$app->user->isGuest === false)) {
					$this->updateActivityStamp();
				}
			}
		];
	}

	/**
	 * Обновление даты-времени активности пользователя.
	 */
	protected function updateActivityStamp() {
		$user = Yii::$app->user->getIdentity();/** @var RefUser $user */
		$utcTimeZone = new DateTimeZone('UTC');
		$dateTimeNow = new DateTime('now', $utcTimeZone);

		if ($user->last_activity_stamp === null) {
			$isNeedUpdate = true;
		}
		else {
			//проверяем, пришло ли время обновления
			$dataTimeInDb = new DateTime($user->last_activity_stamp, $utcTimeZone);
			$isNeedUpdate = (($dateTimeNow->getTimestamp() - $dataTimeInDb->getTimestamp()) > Yii::$app->moduleManager->modules->user->lastActivityUpdatePeriod);
		}

		if ($isNeedUpdate === true) {
			$user->last_activity_stamp = $dateTimeNow->format(DateHelper::DATE_TIME_DATABASE_FORMAT);
			$user->save();
		}
	}
}