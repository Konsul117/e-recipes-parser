<?php

namespace common\modules\recipes\components\downloadProvider;
use common\modules\recipes\components\RecipesLogger;
use proxyProvider\components\ProxyData;
use Yii;

/**
 * Загрузчик страниц по HTTP.
 */
class HttpDownloadProvider implements DownloadProviderInterface {

	/** @var array Куки: название => значение */
	public $cookies = [];

	/** @var int Таймаут соединения со шлюзом */
	public $connectTimeout = 5;

	/** @var int Таймаут ожидания ответа после отправки команды на шлюз */
	public $timeout = 15;

	/** @var string UserAgent */
	public $userAgent = 'Mozilla/5.0 (Windows NT 5.1; rv:31.0) Gecko/20100101 Firefox/31.0';

	/** @var bool Использовать ли прокси */
	public $useProxy = true;

	/**
	 * Загрузка страницы.
	 *
	 * @param string $url URL страницы
	 *
	 * @return LoadedPage
	 */
	public function load($url) {
		$proxy = null;

		$result = null;

		if ($this->useProxy === true) {
			do {
				$proxy = Yii::$app->moduleManager->modules->recipes->proxyProviderPool->getProxy();

				if ($proxy !== null) {
					RecipesLogger::add('Прокси: ' . $proxy->getAddressString());
					$result = $this->loadInner($url, $proxy);

					if ($result->isLoadedSuccess === false) {
						RecipesLogger::add('Загрузить страницу через прокси ' . $proxy->getAddressString() . ' не удалось');
					}

					Yii::$app->moduleManager->modules->recipes->proxyProviderPool->addProxyStat($proxy->id, ($result !== null));
				}
			} while ($result->isLoadedSuccess === false && $proxy !== null);
		}
		else {
			$result = $this->loadInner($url);
		}

		return $result;
	}

	/**
	 * Внутренняя реализация загрузки страницы.
	 *
	 * @param  string        $url   URL страницы
	 * @param ProxyData|null $proxy Прокси или null, если не нужно его исопльзовать
	 *
	 * @return LoadedPage
	 */
	protected function loadInner($url, ProxyData $proxy = null) {
		$page = new LoadedPage();

		$page->isLoadedSuccess = false;
		$page->url             = $url;

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

		$cookieFile = tempnam("/tmp", "e-recipes_cookie_file");

		curl_setopt($curl, CURLOPT_COOKIEJAR, $cookieFile);

		if ($proxy !== null) {
			curl_setopt($curl, CURLOPT_PROXY, $proxy->getAddressString());
		}

		$curlResult = curl_exec($curl);

		$curlCode = curl_errno($curl);

		$page->curlCode = $curlCode;

		$responseHttpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

		$page->httpCode = $responseHttpCode;

		curl_close($curl);

		if (file_exists($cookieFile)) {
			unlink($cookieFile);
		}

		if ($curlCode !== 0) {
			RecipesLogger::add('Ошибка загрузки страницы ' . $url . ': code ' . $curlCode);
			if ($proxy !== null) {
				RecipesLogger::add('Используемый прокси-сервер: ' . $proxy->getAddressString());
			}

			return $page;
		}

		if ($responseHttpCode !== 200) {
			RecipesLogger::add('Ошибка загрузки страницы ' . $url . ': Http-код ' . $responseHttpCode);
			if ($proxy !== null) {
				RecipesLogger::add('Используемый прокси-сервер: ' . $proxy->getAddressString());
			}

			return $page;
		}

		//иногда http-код передаётся в теле страницы, хотя в заголовке 200, поэтому проверяем
		if (preg_match('/^HTTP\/1.?[0-9]? [4-5][0-9][0-9]/', $curlResult) === 1) {
			$page->httpCode = 400;
			return $page;
		}

		$page->isLoadedSuccess = true;
		$page->body            = $curlResult;

		return $page;
	}
}