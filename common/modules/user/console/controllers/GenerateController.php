<?php

namespace common\modules\user\console\controllers;

use common\modules\game\components\AuthorRule;
use Yii;
use yii\console\Controller;
use yii\helpers\ArrayHelper;
use yii\rbac\Rule;

/**
 * Контролер генерации прав и назначений на роли.
 */
class GenerateController extends Controller {

	protected $permissionsFileMaps = [
		'*/modules/*/config/acl_permissions.php',
	];

	protected $assignmentsFileMaps = [
		'*/modules/*/config/acl_assignments.php',
	];

	/**
	 * Генерация.
	 */
	public function actionIndex() {
		$this->stderr('Загружаем все права и связки с ролями' . PHP_EOL);

		$permissions = [];

		foreach ($this->permissionsFileMaps as $pattern) {
			foreach(glob($pattern) as $path) {
				$permissions = ArrayHelper::merge($permissions, require($path));
			}
		}

		$assignments = [];

		foreach ($this->assignmentsFileMaps as $pattern) {
			foreach(glob($pattern) as $path) {
				$assignments = ArrayHelper::merge($assignments, require($path));
			}
		}

		$this->stdout('Найдено ' . count($permissions) . ' прав и ' . count($assignments) . ' связок с ролями' . PHP_EOL);

		$auth = Yii::$app->authManager;

		//добавляем новые права

		foreach($permissions as $permissionRow) {
			$permission = $auth->getPermission($permissionRow['permission']);
			if ($permission !== null) {
				if (isset($permissionRow['rule'])) {
					$rule = $this->addOrUpdateRule($permissionRow['rule']);
					$permission->ruleName = $rule->name;
				}
				elseif ($permission->ruleName) {
					$rule = Yii::$app->authManager->getRule($permission->ruleName);

					if ($rule !== null) {
						Yii::$app->authManager->remove($rule);
					}

					$permission->ruleName = null;
				}

				$this->stdout('Обновляем право ' . $permissionRow['permission'] . ' ... ');
				if ($auth->update($permission->name, $permission) === true) {
					$this->stdout('успешно');
				}
				else {
					$this->stdout('Ошибка при добавлении');

					return;
				}
			}
			else {

				$this->stdout('Добавляем право ' . $permissionRow['permission'] . ' ... ');

				$permission              = $auth->createPermission($permissionRow['permission']);
				$permission->description = $permissionRow['name'];

				//добавляем правило, если оно есть
				if (isset($permissionRow['rule'])) {
					$rule = $this->addOrUpdateRule($permissionRow['rule']);
					$permission->ruleName = $rule->name;
				}

				if ($auth->add($permission)) {
					$this->stdout('успешно');
				}
				else {
					$this->stdout('Ошибка при добавлении');

					return;
				}
			}

			$this->stdout(PHP_EOL);
		}

		//добавляем новые связки между правами и ролями

		$permissionsByRoles = [];

		foreach($assignments as $assignment) {
			if (!isset($permissionsByRoles[$assignment['role']])) {
				$permissionsByRoles[$assignment['role']] = $auth->getPermissionsByRole($assignment['role']);
			}

			$assigned = false;

			foreach($permissionsByRoles[$assignment['role']] as $permission) {
				if ($permission->name === $assignment['permission']) {
					$assigned = true;
				}
			}

			if (!$assigned) {
				$this->stdout('Связка между ролью ' . $assignment['role'] . ' и правом ' . $assignment['permission'] . ' отсутствует, добавляем ... ');
				$role = $auth->getRole($assignment['role']);

				if ($role === null) {
					$this->stdout('Роль не найдена: ' . $assignment['role'] . PHP_EOL);

					return;
				}

				$permission = $auth->getPermission($assignment['permission']);

				if ($permission === null) {
					$this->stdout('Право не найдено: ' . $assignment['permission'] . PHP_EOL);

					return;
				}

				if ($auth->addChild($role, $permission)) {
					$this->stdout('успешно');
				}
				else {
					$this->stdout('ошибка');
				}

				$this->stdout(PHP_EOL);
			}
		}
	}

	/**
	 * Добавление или обновление правило.
	 *
	 * @param array $ruleRow Массив-конфиг для правила
	 *
	 * @return Rule|null Объект правила или null в случае неуспеха
	 */
	protected function addOrUpdateRule($ruleRow) {
		$authorRule = Yii::$app->authManager->getRule($ruleRow['class']);/** @var AuthorRule $authorRule */
		if ($authorRule === null) {
			$this->stdout('Добавляем правило ' . $ruleRow['class'] . ' ... ');
			$authorRule = Yii::createObject($ruleRow);
			Yii::$app->authManager->add($authorRule);
		}
		else {
			$this->stdout('Обновляем правило ' . $ruleRow['class'] . ' ... ');
			Yii::$app->authManager->update($authorRule->name, $authorRule);
		}

		$this->stdout('успешно' . PHP_EOL);

		return $authorRule;
	}

}