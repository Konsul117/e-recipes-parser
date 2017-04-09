<?php

namespace common\modules\recipes\frontend\models\flavorSearch;

/**
 * Ответ на запрос получения ароматизаторов.
 */
class FlavorsResponse {

	/** @var FlavorItemResponse[] Ароматизаторы */
	public $flavors = [];

	/** @var int Общее количество найденных варианатов */
	public $totalCount;
}