<?php

namespace common\modules\recipes;

use proxyProvider\components\ProxyProviderPool;
use yiiCustom\base\Module;

/**
 * Модуль рецептов.
 *
 * @property-read ProxyProviderPool $proxyProviderPool Пул провайдеров прокси
 */
class Recipes extends Module {

	/** @var int|null Идентификатор текущего обрабатываемого источника. Если null, то данные не переданы */
	public $currentSourceId;
}