<?php

namespace common\modules\recipes\components;

use common\modules\recipes\components\proxyProvider\AbstractProxyProvider;
use common\modules\recipes\components\proxyProvider\ProxyData;
use common\modules\recipes\models\Source;
use ReflectionClass;
use yii\base\Exception;
use yiiCustom\logger\LoggerStream;

abstract class AbstractGrabber {

	/** @var array Куки: название => значение */
	public $cookies = [];

	/** @var int Таймаут соединения со шлюзом */
	public $connectTimeout = 5;

	/** @var int Таймаут ожидания ответа после отправки команды на шлюз */
	public $timeout = 10;

	/** @var string UserAgent */
	public $userAgent = 'Mozilla/5.0 (Windows NT 5.1; rv:31.0) Gecko/20100101 Firefox/31.0';

	/** @var bool Использовать ли прокси */
	public $useProxy = true;

	/** @var bool Нужно ли обновлять уже загруженные ароматизаторы */
	public $isNeedToUpdateFlavors = false;

	/** @var string|null Путь к файлу для cookie, если null, то приём кук не будет осуществляться */
	protected $cookieFile;

	/** @var LoggerStream Логгер */
	protected $logger;

	/** @var Source Модель источника */
	protected $source;

	/** @var AbstractProxyProvider Провайдер прокси */
	protected $proxyProvider;

	protected function __construct() {}

	/**
	 * Получение граббер для источника.
	 *
	 * @param int          $sourceId Идентификатор источника
	 * @param LoggerStream $logger   Логгер
	 *
	 * @return self|null Граббер или null, если граббер для указанного источника отсутствует
	 */
	public static function getGrabber($sourceId, LoggerStream $logger, AbstractProxyProvider $proxyProvider) {
		$source = Source::findOne([Source::ATTR_ID => $sourceId]);/** @var Source $source */

		if ($source === null) {
			return null;
		}

		$r = new ReflectionClass(static::class);

		$grabberClassName = $r->getNamespaceName() . '\\' . $source->tech_name . 'Grabber';

		$grabber = new $grabberClassName;
		if (($grabber instanceof static) === false) {
			return null;
		}/** @var AbstractGrabber $grabber */

		$grabber->source        = $source;
		$grabber->logger        = $logger;
		$grabber->proxyProvider = $proxyProvider;

		return $grabber;
	}

	/**
	 * Получение страницы.
	 *
	 * @param string $url URL страницы
	 *
	 * @return string|null Контент или null в случае ошибки
	 *
	 * @throws Exception
	 */
	protected function load($url) {
//		shuffle($proxyList);
		$proxy = null;

		if ($this->useProxy === true) {
			$proxyList = $this->proxyProvider->getProxyList();

			if (count($proxyList) > 0) {
				do {
					$proxy = array_shift($proxyList);
					$this->logger->log('Прокси: ' . $proxy->getAddressString());
					$result = $this->loadInner($url, $proxy);

					if ($result === null) {
						$this->proxyProvider->banProxy($proxy->id);
					}
				}
				while ($result === null && (count($proxyList) > 0));
			}
			else {
				throw new Exception('Не удалось получить список прокси');
			}
		}
		else {
			$result = $this->loadInner($url);
		}

		return $result;
	}

	protected function loadInner($url, ProxyData $proxy = null) {
		$curl = curl_init();

		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, $this->connectTimeout);
		curl_setopt($curl, CURLOPT_TIMEOUT, $this->timeout);
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($curl, CURLOPT_USERAGENT, $this->userAgent);

		$cookieString = '';
		$i = 0;
		foreach ($this->cookies as $param => $value) {
			$cookieString .= $param . '=' . $param;

			if ($i < count($this->cookies) - 1) {
				$cookieString .= ';';
			}

			$i++;
		}

		curl_setopt($curl, CURLOPT_COOKIE, $cookieString);

		if ($this->cookieFile !== null) {
			curl_setopt($curl, CURLOPT_COOKIEJAR, $this->cookieFile);
		}

		if ($proxy !== null) {
			curl_setopt($curl, CURLOPT_PROXY, $proxy->getAddressString());
		}

		$curlResult = curl_exec($curl);

		$errNo = curl_errno($curl);

		$responseHttpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

		curl_close($curl);

		if ($errNo !== 0) {
			$this->logger->log('Ошибка загрузки страницы ' . $url . ': code ' . $errNo, LoggerStream::TYPE_ERROR);
			if ($proxy !== null) {
				$this->logger->log('Используемый прокси-сервер: ' . $proxy->getAddressString(), LoggerStream::TYPE_ERROR);
			}

			return null;
		}

		if ($responseHttpCode !== 200) {
			$this->logger->log('Ошибка загрузки страницы ' . $url . ': Http-код ' . $responseHttpCode, LoggerStream::TYPE_ERROR);
			if ($proxy !== null) {
				$this->logger->log('Используемый прокси-сервер: ' . $proxy->getAddressString(), LoggerStream::TYPE_ERROR);
			}

			return null;
		}

		//иногда http-код передаётся в теле страницы, хотя в заголовке 200, поэтому проверяем
		if (preg_match('/^HTTP\/1.?[0-9]? [4-5][0-9][0-9]/', $curlResult) === 1) {
			return null;
		}

		return $curlResult;
	}

	/**
	 * Запуск граббинга.
	 */
	abstract public function start();
}