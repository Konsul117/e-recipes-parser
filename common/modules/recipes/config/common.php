<?php

use common\modules\recipes\Recipes;

return [
	'modules' => [
		'recipes' => [
			'class'      => Recipes::class,
			'components' => [
				'proxyProvider' => [
					'class' => \common\modules\recipes\components\proxyProvider\FreeProxyListProvider::class,
//					'class' => \common\modules\recipes\components\proxyProvider\GetProxyListProvider::class,
					'token' => 'demo',//freeproxy
//					'key' => '8vmnfdemafuwipo6sa7drwdqmbhmyw',//spinproxies.com
				],
			],
		],
	],
];