<?php
Yii::setAlias('theme', dirname(dirname(__DIR__)) . '/frontend/themes/default');
return [
	'id'         => 'frontend',
	'components' => [
		'errorHandler' => [
			'errorView' => '@frontend/views/layouts/error.php',
		],
		'request'      => [
			'cookieValidationKey' => 'sugoh6an1rees6Booqua',
		],
	],
];