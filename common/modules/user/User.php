<?php

namespace common\modules\user;

use common\modules\user\backend\controllers\SettingsController;
use common\modules\user\frontend\controllers\AuthController;
use common\modules\user\frontend\controllers\RegisterController;
use common\modules\user\frontend\widgets\UserInfoWidget;
use common\modules\user\models\RefUser;
use common\modules\user\models\UserData;
use Yii;
use yiiCustom\base\Module;
use yiiCustom\core\ConfigCollector;
use yiiCustom\models\RefModuleSetting;

/**
 * Модуль пользователей (авторизация, распределение прав и пр.).
 *
 * @property int $lastActivityUpdatePeriod            Период обновления метки последней активности пользователя (в секундах)
 * @property int $onlineStatusSinceLastActivityPeriod Период после последней активности пользователя,
 *                                                    в течение которого он считается в онлайне (в секундах)
 */
class User extends Module {
	
	/** Право на доступ в админку */
	const P_BACKEND_ACCESS = 'backend_access';

	/** @var string Основная роль для доступа в админку. Если не задано, то доступ не будет проверяться */
	public $commonRoleAccess = null;
	
	/** @var string[] Список идентификаторов ролей */
	public $roles;

	/** @var UserData[] Runtime-кэш по запрошенным пользователем  */
	protected $usersInfoCache = [];

	public function settings() {
		return [
			'lastActivityUpdatePeriod' => [
				'title'         => 'Период обновления метки последней активности пользователя (в секундах)',
				'type_cast'     => RefModuleSetting::TYPE_INT,
				'default_value' => 300,
			],
			'onlineStatusSinceLastActivityPeriod' => [
				'title'         => 'Период после последней активности пользователя, в течение которого он считается в онлайне (в секундах)',
				'type_cast'     => RefModuleSetting::TYPE_INT,
				'default_value' => 900,
			],
		];
	}

	/**
	 * Получение URL страницы авторизации.
	 * 
	 * @param bool $repeat Повторная авторизация
	 *
	 * @return string|null
	 */
	public function getAuthUrl($repeat = false) {
		if (ConfigCollector::getEntryPoint() === ConfigCollector::ENTRY_POINT_FRONTEND) {
			return AuthController::getActionUrl(AuthController::ACTION_INDEX);
		}
		elseif (ConfigCollector::getEntryPoint() === ConfigCollector::ENTRY_POINT_BACKEND) {
			return \common\modules\user\backend\controllers\AuthController::getActionUrl(
				\common\modules\user\backend\controllers\AuthController::ACTION_INDEX,
				[\common\modules\user\backend\controllers\AuthController::PARAM_INDEX_REPEAT => $repeat]
			);
		}

		return null;
	}

	/**
	 * Получение URL страницы регистраци.
	 *
	 * @return string
	 */
	public function getRegisterUrl() {
		return RegisterController::getActionUrl(RegisterController::ACTION_INDEX);
	}

	/**
	 * Получение URL разлогинивания пользователя
	 *
	 * @return string|null
	 */
	public function getLogoutUrl() {
		if (ConfigCollector::getEntryPoint() === ConfigCollector::ENTRY_POINT_FRONTEND) {
			return AuthController::getActionUrl(AuthController::ACTION_LOGOUT);
		}
		elseif (ConfigCollector::getEntryPoint() === ConfigCollector::ENTRY_POINT_BACKEND) {
			return \common\modules\user\backend\controllers\AuthController::getActionUrl(\common\modules\user\backend\controllers\AuthController::ACTION_LOGOUT);
		}

		return null;
	}

	/**
	 * Получение страницы отсутствия доступа админки.
	 * 
	 * @return string
	 */
	public function getBackendNoAccessUrl() {
		return \common\modules\user\backend\controllers\AuthController::getActionUrl(\common\modules\user\backend\controllers\AuthController::ACTION_NO_ACCESS);
	}

	/**
	 * Получение URL настроек модуля.
	 *
	 * @return string
	 */
	public function getSettingsBackendUrl() {
		return SettingsController::getActionUrl(SettingsController::ACTION_INDEX);
	}

	/**
	 * Получение модели текущего залогиненного пользователя.
	 *
	 * @return UserData|null Модель пользователя или null, если пользователь не залогинен.
	 */
	public function getAuthorizedUser() {
		if (Yii::$app->user->isGuest === true) {
			return null;
		}

		$userModel = Yii::$app->user->identity;/** @var RefUser $userModel */

		return new UserData($userModel);
	}

	/**
	 * Получение данных о пользователе по его идентификатору.
	 *
	 * @param string $userId Идентификатор пользователя
	 *
	 * @return UserData|null Модель данных или null, если пользователь не найден
	 */
	public function getUserById($userId) {
		if (array_key_exists($userId, $this->usersInfoCache) === false) {
			$refUser = RefUser::findOne($userId);/** @var RefUser $refUser */

			if ($refUser !== null) {
				$this->usersInfoCache[$userId] = new UserData($refUser);
			}
			else {
				$this->usersInfoCache[$userId] = null;
			}
		}

		return $this->usersInfoCache[$userId];
	}

	/**
	 * Получение моделей нескольких пользователей по их идентификаторам.
	 * Метод нужен, чтобы получить данные для списка пользователей.
	 *
	 * @param int[] $usersIds Идентификаторы пользователей
	 *
	 * @return UserData[] Массив моделей пользователей, индексированный по идентификаторам
	 */
	public function getUsersByIds($usersIds) {
		$result = [];

		$usersIdsForSearch = $usersIds;

		foreach ($usersIdsForSearch as $i => $userId) {
			if (array_key_exists($userId, $this->usersInfoCache) === true) {
				$result[$userId] = $this->usersInfoCache[$userId];

				unset($usersIdsForSearch[$i]);
			}
		}

		if (count($usersIdsForSearch) > 0) {
			$refUsers = RefUser::findAll([RefUser::ATTR_ID => $usersIdsForSearch]);

			foreach ($refUsers as $refUser) {
				$result[$refUser->id] = new UserData($refUser);

				$this->usersInfoCache[$refUser->id] = $result[$refUser->id];
			}
		}

		return $result;
	}

	/**
	 * Поиск пользователя по подтверждённому номеру телефона.я
	 *
	 * @param string $phoneNumber Номер телефона
	 *
	 * @return UserData|null Модель пользователя или null, если пользователь не найден
	 */
	public function findUserByPhoneNumber($phoneNumber) {
		$user = RefUser::findOne([
			RefUser::ATTR_PHONE              => $phoneNumber,
			RefUser::ATTR_PHONE_IS_CONFIRMED => true,
		]);

		if ($user !== null) {
			$userData = new UserData($user);

			$this->usersInfoCache[$user->id] = $userData;

			return $userData;
		}

		return null;
	}

	/**
	 * Отрисовка виджета информации о пользователя.
	 *
	 * @param int $userId Идентификатор пользователя
	 *
	 * @return string
	 */
	public function drawUserInfoWidget($userId) {
		return (new UserInfoWidget([UserInfoWidget::ATTR_USER_ID => $userId]))->run();
	}

	/**
	 * Получение URL профиля пользователя.
	 *
	 * @param UserData $user Модель пользователя
	 *
	 * @return string
	 */
	public function getUserProfileFrontendUrl(UserData $user) {
		//todo Реализовать возврат ссылки на профиль (когда будет сделан)
		return '';
	}

	/**
	 * Назначаение юзеру номера телефона и признака подтверждения.
	 *
	 * @param int    $userId      Идентификатор пользователя
	 * @param string $phoneNumber Номер телефона
	 * @param bool   $isConfirmed Подтверждён ли номер
	 *
	 * @return bool Успешность выполнения операции
	 */
	public function setUserPhoneNumber($userId, $phoneNumber, $isConfirmed = false) {
		$user = RefUser::findOne($userId);

		if ($user === null) {
			return false;
		}

		$user->phone              = $phoneNumber;
		$user->phone_is_confirmed = $isConfirmed;

		return $user->save();
	}
}