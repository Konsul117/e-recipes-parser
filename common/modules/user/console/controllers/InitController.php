<?php

namespace common\modules\user\console\controllers;

use common\modules\user\components\AclHelper;
use common\modules\user\models\RefUser;
use Yii;
use yii\base\InvalidParamException;
use yii\console\Controller;
use yii\console\Exception;
use yii\db\Migration;
use yii\helpers\Console;

/**
 * Контроллер ACL.
 */
class InitController extends Controller {

	protected $rbacMigrationPath  = '@yii/rbac/migrations/';
	protected $rbacMigrationClass = 'm140506_102106_rbac_init';

	/**
	 * Первичная инициализация ACL-структуры.
	 */
	public function actionCreateAclTables() {
		$this->stdout('Создаём структуру таблиц ACL' . PHP_EOL);

		if ($this->getMigration($this->rbacMigrationPath, $this->rbacMigrationClass)->up() !== false) {
			$this->stdout('Успешно' . PHP_EOL, Console::FG_GREEN);
		}
		else {
			$this->stdout('Ошибка' . PHP_EOL, Console::FG_RED);

			return;
		}

		$this->stdout('Завершено' . PHP_EOL, Console::FG_GREEN);
	}

	/**
	 * Откат таблиц ACL-структуры.
	 */
	public function actionRemoveAclTables() {
		$this->stdout('Удаляем структуру таблиц ACL' . PHP_EOL);

		if ($this->getMigration($this->rbacMigrationPath, $this->rbacMigrationClass)->down() !== false) {
			$this->stdout('Успешно' . PHP_EOL, Console::FG_GREEN);
		}
		else {
			$this->stdout('Ошибка' . PHP_EOL, Console::FG_RED);

			return;
		}

		$this->stdout('Завершено' . PHP_EOL, Console::FG_GREEN);
	}

	/**
	 * Инициализация ролей.
	 *
	 * @throws \yii\db\Exception
	 */
	public function actionInitRoles() {
		$transaction = Yii::$app->db->beginTransaction();

		try {
			$this->stdout('Инициализируем роли' . PHP_EOL);

			$this->initRoles();

			$this->stdout('Завершено' . PHP_EOL, Console::FG_GREEN);

			$transaction->commit();
		}
		catch (Exception $e) {
			$transaction->rollBack();
			$this->stderr('Исключение: ' . $e . PHP_EOL);
		}
	}

	/**
	 * Назначение роли админа пользователю
	 *
	 * @param string $username Имя пользователя (логин)
	 */
	public function actionAssignAdminRole($username) {
		/** @var RefUser $user */
		$user = RefUser::findOne([RefUser::ATTR_USERNAME => $username]);

		if ($user === null) {
			$this->stderr('Пользователь ' . $username . ' не найден' . PHP_EOL, Console::FG_RED);

			return;
		}

		$auth = Yii::$app->authManager;

		$adminRole = $auth->getRole(AclHelper::ROLE_ADMIN);

		try {
			$auth->assign($adminRole, $user->id);
		}
		catch (\Exception $e) {
			$this->stderr('Исключение при попытке назначить роль админа юзеру ' . $username . ': ' . $e->getMessage() . PHP_EOL);
		}

		$this->stdout('Роль админа успешно добавлена юзеру ' . $username . PHP_EOL);
	}

	/**
	 * Инициализация ролей.
	 *
	 * @throws Exception
	 */
	protected function initRoles() {
		$auth = Yii::$app->authManager;

		$this->stdout('Создаём роли' . PHP_EOL);

		foreach (Yii::$app->moduleManager->modules->user->roles as $roleId) {
			$this->stdout('Роль ' . $roleId . "\t");
			if ($auth->getRole($roleId) === null) {
				$role = $auth->createRole($roleId);
				if ($auth->add($role)) {
					$this->stdout('создана успешно');
				}
				else {
					$this->stdout('ошибка');

					throw new Exception('Ошибка при создании роли');
				}
			}
			else {
				$this->stdout('уже существует');
			}

			$this->stdout(PHP_EOL);
		}
	}

	/**
	 * Получить объект миграции
	 *
	 * @param string $path  Путь к файлу миграции
	 * @param string $class Имя класса
	 *
	 * @return Migration объект миграции
	 */
	protected function getMigration($path, $class) {
		$file = Yii::getAlias($path . DIRECTORY_SEPARATOR . $class . '.php');

		if (file_exists($file) === false) {
			throw new InvalidParamException('Файл миграцим ' . $file . ' (путь: ' . $path . ') не существует');
		}

		require_once($file);

		if (class_exists($class) === false) {
			throw new InvalidParamException('Миграция ' . $class . ' (путь: ' . $path . ') не существует');
		}

		/** @var Migration $mirgation */
		$mirgation = new $class();

		return $mirgation;
	}

}