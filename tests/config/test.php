<?php
use yiiCustom\core\ConfigCollector;

$test =  [
	'id'         => 'tests',
	'components' => [
		'db' => [
			'dsn'      => 'mysql:host=localhost;port=3306;dbname=e-recipes-test',
			'username' => 'root',
			'password' => 'qwe',
		],
		'urlManager' => [
			'baseUrl'  => '/',
			'hostInfo' => 'http://ongame.loc',
		],
	],
	'controllerMap' => [
		'migrate'        => \yiiCustom\console\controllers\MigrateController::class,
		'moduleSettings' => yiiCustom\console\controllers\ModuleSettingsInitController::class,
	],
];

$config = ConfigCollector::getApplicationConfig();

return \yii\helpers\ArrayHelper::merge($config, $test);