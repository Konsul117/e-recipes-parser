<?php

use common\modules\recipes\Recipes;
use proxyProvider\components\FreeProxyListProvider;
use proxyProvider\components\HideMeProvider;
use proxyProvider\components\ProxyProviderPool;

return [
	'modules' => [
		'recipes' => [
			'class'      => Recipes::class,
			'components' => [
				'proxyProviderPool' => [
					'class' => ProxyProviderPool::class,
					ProxyProviderPool::ATTR_PROVIDERS_CONFIGS => [
						[
							'class' => FreeProxyListProvider::class,
							'token' => 'demo',
						],
//						[
//							'class' => \common\modules\recipes\components\proxyProvider\GetProxyListProvider::class,
//						],
//						[
//							'class' => \common\modules\recipes\components\proxyProvider\SpinProxiesProvider::class,
//							'key' => '8vmnfdemafuwipo6sa7drwdqmbhmyw',
//						],
//						[
//							'class' => \common\modules\recipes\components\proxyProvider\GimmeProxyProvider::class,
//						],
						[
							'class' => HideMeProvider::class,
						],
					],
				],
			],
		],
	],
];