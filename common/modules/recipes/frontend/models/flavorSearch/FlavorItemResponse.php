<?php

namespace common\modules\recipes\frontend\models\flavorSearch;

/**
 * Обёртка для ароматизатора в ответе на запрос.
 */
class FlavorItemResponse {

	/** @var int Идентификатор  */
	public $id;

	/** @var string Название */
	public $name;

	/** @var int Идентификатор бренда */
	public $brandId;

	/** @var int[] Связанные источники */
	public $sourcesIds = [];

}