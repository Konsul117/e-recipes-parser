<?php
use common\modules\user\components\AclHelper;

return [
	[
		'role'       => AclHelper::ROLE_ADMIN,
		'permission' => \common\modules\user\User::P_BACKEND_ACCESS,
	],
	[
		'role'       => AclHelper::ROLE_MANAGER,
		'permission' => \common\modules\user\User::P_BACKEND_ACCESS,
	],
];