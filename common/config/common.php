<?php
use yiiCustom\base\FileLogTargetSimple;
use yiiCustom\core\ConfigManager;
use yiiCustom\core\Environment;
use yiiCustom\core\ModuleManager;
use yiiCustom\core\Theme;
use yiiCustom\core\View;

$result = [
	'bootstrap'  => [
		'log',
		'moduleManager',
	],
	'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
	'language'   => 'ru',
	'components' => [
		'view'          => [
			'class' => View::class,
			'theme' => [
				'class' => Theme::class,
				'pathMap' => [
					'@app/widgets' => '@theme',
				]
			],
		],
		'configManager' => [
			'class' => ConfigManager::class,
		],
		'urlManager'    => [
			'enablePrettyUrl' => true,
			'showScriptName'  => false,
		],
		'db'            => [
			'class'               => \yii\db\Connection::class,
			'charset'             => 'utf8mb4',
			'dsn'                 => '',
			'username'            => '',
			'password'            => '',
			'enableSchemaCache'   => true,
			'schemaCacheDuration' => 24 * 60 * 60,
		],
		'moduleManager' => [
			'class' => ModuleManager::class,
		],
		'log'           => [
			'targets' => [
				'run' => [
					'class'      => FileLogTargetSimple::class,
					'levels'     => ['error', 'warning'],
					'categories' => [],
					'logFile'    => '@runtime/logs/run.log',
				],
			],
		],
		'env' => [
			'class' => Environment::class,
		],
	],
	'params' => [
		'localTimezoneOffset' => 3,
		'baseDomain'          => 'recipes.loc',
	],
];

if (YII_DEBUG) {
	$result['bootstrap'][] = 'debug';
	$result['modules']['debug'] = [
		'class'      => \yii\debug\Module::class,
		'allowedIPs' => ['*'],
	];
}

return $result;