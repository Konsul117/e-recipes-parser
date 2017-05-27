<?php
return [
	'id'            => 'console',
	'controllerMap' => [
		'migrate'        => \yiiCustom\console\controllers\MigrateController::class,
		'moduleSettings' => yiiCustom\console\controllers\ModuleSettingsInitController::class,
	],
	'components'    => [
		'urlManager' => [
			'baseUrl'  => '/',
			'hostInfo' => 'http://ongame.loc',
		],
	],
];