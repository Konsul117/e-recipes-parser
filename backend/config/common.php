<?php

return [
	'id'         => 'backend',
	'components' => [
		'request'      => [
			'enableCookieValidation' => false,
			'enableCsrfValidation'   => false,
			'cookieValidationKey'    => 'sugoh6an1rees6Booqua',
		],
		'errorHandler' => [
			'errorView' => '@backend/views/layouts/error.php',
		],
	],
];