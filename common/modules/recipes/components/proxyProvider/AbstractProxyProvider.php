<?php

namespace common\modules\recipes\components\proxyProvider;

use common\modules\recipes\exceptions\ProxyProviderDataNotReceivedException;
use yii\base\Component;

/**
 * Суперкласс для провайдера прокси.
 */
abstract class AbstractProxyProvider extends Component {

	/**
	 * Получение списка прокси.
	 *
	 * @return ProxyData[]
	 *
	 * @throws ProxyProviderDataNotReceivedException
	 */
	abstract public function getProxyList();

	/**
	 * Забанить прокси (когда он был недоступен).
	 *
	 * @param string $proxyId Идентификатор прокси
	 */
	abstract public function banProxy($proxyId);

}