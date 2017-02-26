<?php

namespace common\modules\recipes\components\proxyProvider;

use common\modules\recipes\exceptions\ProxyProviderDataNotReceivedException;
use Faker\Provider\Uuid;
use Yii;
use yii\base\InvalidConfigException;

/**
 * Провайдер прокси "FreeProxyList".
 *
 * @link http://www.freeproxy-list.ru
 */
class FreeProxyListProvider extends AbstractProxyProvider {

	/** Основной URL получения данных */
	const BASE_URL = 'http://www.freeproxy-list.ru/api/proxy';

	/** Период кэширования */
	const CACHE_DURATION = 15 * 60;

	/** @var string Токен доступа к сервису */
	public $token;

	/** @var ProxyData[] Акутальный список прокси */
	protected $actualProxyList;

	/** @var string[] Список идентификаторов прокси, которые забанены */
	protected $proxyBanList = [];

	/**
	 * @inheritdoc
	 */
	public function init() {
		parent::init();

		if ($this->token === null) {
			throw new InvalidConfigException('Отсутствует токен сервиса');
		}
	}

	/**
	 * @inheritdoc
	 */
	public function getProxyList() {
		if ($this->actualProxyList === null) {
			$cacheKey = __METHOD__ . 'v-2';

			$data = Yii::$app->cache->get($cacheKey);
			/** @var ProxyData[] $data */
			$banListCacheKey = $this->getBanListCacheKey();

			if ($data === false) {
				$data = $this->getProxyListFromApi();

				Yii::$app->cache->set($cacheKey, $data, static::CACHE_DURATION);
			}
			else {
				//если список прокси взят из кэша, то список банов для них актуален
				$banList = Yii::$app->cache->get($banListCacheKey);

				if ($banList !== false) {
					$this->proxyBanList = $banList;

					foreach ($data as $id => $proxy) {
						if (in_array($id, $banList)) {
							unset($data[$id]);
						}
					}
				}
			}

			$this->actualProxyList = $data;
		}

		$data = $this->actualProxyList;

		shuffle($data);

		return $data;
	}

	/**
	 * Получение списка прокси из api.
	 *
	 * @return ProxyData[]
	 * @throws ProxyProviderDataNotReceivedException
	 */
	protected function getProxyListFromApi() {
		$queryUrl = static::BASE_URL . '?' . http_build_query([
				'anonymity' => true,
				'count'     => 20,
				'token'     => $this->token,
			]);

		$curl = curl_init();

		curl_setopt($curl, CURLOPT_URL, $queryUrl);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 2);
		curl_setopt($curl, CURLOPT_TIMEOUT, 5);
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);

		$curlResult = curl_exec($curl);

		$errNo = curl_errno($curl);

		$responseHttpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

		curl_close($curl);

		if ($errNo !== 0) {
			throw new ProxyProviderDataNotReceivedException('Ошибка curl: ' . $errNo);
		}

		if ($responseHttpCode !== 200) {
			throw new ProxyProviderDataNotReceivedException('Http code ' . $responseHttpCode);
		}

		$rows = explode("\n", $curlResult);

		$result = [];

		foreach ($rows as $row) {
			$proxy = new ProxyData();

			$dataItems = explode(':', $row);

			$proxy->id      = Uuid::uuid();
			$proxy->address = $dataItems[0];
			$proxy->port    = $dataItems[1];

			$result[$proxy->id] = $proxy;
		}

		return $result;
	}

	/**
	 * @inheritdoc
	 */
	public function banProxy($proxyId) {
		if (array_key_exists($proxyId, $this->actualProxyList)) {
			unset($this->actualProxyList[$proxyId]);

			$this->proxyBanList[] = $proxyId;

			$this->cacheBanList();
		}
	}

	/**
	 * Кэширование списка бана.
	 */
	protected function cacheBanList() {
		$cacheKey = $this->getBanListCacheKey();

		if (count($this->proxyBanList) > 0) {
			Yii::$app->cache->set($cacheKey, $this->proxyBanList, static::CACHE_DURATION);
		}
		else {
			Yii::$app->cache->delete($cacheKey);
		}
	}

	/**
	 * Получение ключа кэша для списка бана.
	 *
	 * @return string
	 */
	protected function getBanListCacheKey() {
		return __METHOD__ . '.v-1';
	}
}