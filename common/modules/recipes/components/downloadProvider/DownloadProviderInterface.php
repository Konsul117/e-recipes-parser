<?php


namespace common\modules\recipes\components\downloadProvider;

/**
 * Интерфейс провайдера загрузки страниц.
 */
interface DownloadProviderInterface {

	/**
	 * Загрузка страницы.
	 *
	 * @param string $url URL страницы
	 *
	 * @return LoadedPage
	 */
	public function load($url);
}