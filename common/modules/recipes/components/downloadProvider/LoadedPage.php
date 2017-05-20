<?php

namespace common\modules\recipes\components\downloadProvider;

/**
 * Обёртка загруженной страницы.
 */
class LoadedPage {

	/** @var string Тело загруженной страницы */
	public $body;

	/** @var int Код curl результата загрузки */
	public $curlCode;

	/** @var int HTTP-код результата загрузки */
	public $httpCode;

	/** @var float Время ожидания ответа (с) */
	public $responseTime;

	/** @var string URL страницы */
	public $url;
}