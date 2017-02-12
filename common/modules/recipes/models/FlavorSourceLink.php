<?php

namespace common\modules\recipes\models;

use yiiCustom\base\ActiveRecord;

/**
 * Связь между ароматизатором и источниками.
 *
 * @property int $flavor_id        Идентификатор ароматизатора
 * @property int $source_id        Идентификатор источника
 * @property int $source_flavor_id Идентификатор ароматизатора в системе источника
 */
class FlavorSourceLink extends ActiveRecord {

	const ATTR_FLAVOR_ID        = 'flavor_id';
	const ATTR_SOURCE_ID        = 'source_id';
	const ATTR_SOURCE_FLAVOR_ID = 'source_flavor_id';

}