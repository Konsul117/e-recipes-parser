<?php

use common\modules\recipes\Recipes;

return [
	'modules' => [
		'recipes' => [
			'class'      => Recipes::class,
			'proxyList'  => [],
			'components' => [
				'freeProxyList' => [
					'class' => \common\modules\recipes\components\proxyProvider\FreeProxyListProvider::class,
					'token' => 'demo',
				],
			],
		],
	],
];