<?php

namespace common\modules\recipes\components\proxyProvider;

/**
 * Модель-обёртка для данных о прокси-сервере.
 */
class ProxyData {

	/** @var string Идентификатор прокси */
	public $id;

	/** @var string IP-адрес или домен прокси */
	public $address;

	/** @var string Порт прокси */
	public $port;

	/**
	 * Получение строки адреса: адрес:порт
	 *
	 * @return string
	 */
	public function getAddressString() {
		return $this->address . ':' . $this->port;
	}

}