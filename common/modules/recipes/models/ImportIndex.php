<?php

namespace common\modules\recipes\models;

use yiiCustom\base\ActiveRecord;

/**
 * Индекс импортрованных сущностей.
 *
 * @property int    $site_id        Идентификатор на сайте
 * @property string $remote_id      Идентификатор на внешнем ресурсе
 * @property int    $entity_type_id Идентификатор типа сущности
 */
class ImportIndex extends ActiveRecord {

	const ATTR_SITE_ID        = 'site_id';
	const ATTR_REMOTE_ID      = 'remote_id';
	const ATTR_ENTITY_TYPE_ID = 'entity_type_id';

}