<?php
use common\modules\user\components\AclHelper;
use common\modules\user\models\RefUser;
use common\modules\user\User;
use yii\rbac\DbManager;

return [
	'modules'    => [
		'user' => [
			'class' => User::class,
			'roles' => [
				AclHelper::ROLE_BUYER,
				AclHelper::ROLE_SELLER,
				AclHelper::ROLE_MANAGER,
				AclHelper::ROLE_ADMIN,
			],
		],
	],
	'components' => [
		'authManager' => [
			'class'           => DbManager::class,
			'itemTable'       => 'ref_user_role',
			'ruleTable'       => 'ref_user_rule',
			'itemChildTable'  => 'reg_user_role_child',
			'assignmentTable' => 'reg_user_assignment',
		],

		'user' => [
			'class'           => \yii\web\User::class,
			'identityClass'   => RefUser::class,
			'enableAutoLogin' => true,
			'loginUrl'        => ['user/auth'],
		],
	],
];