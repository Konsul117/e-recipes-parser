<?php

namespace common\modules\recipes\models;

use yiiCustom\base\ActiveRecord;

/**
 * Источники.
 *
 * @property int    $id        Уникальный идентификатор
 * @property string $title     Название
 * @property string $url       Адрес источника
 * @property string $tech_name Техническое название
 */
class Source extends ActiveRecord {

	const ATTR_ID        = 'id';
	const ATTR_TITLE     = 'title';
	const ATTR_URL       = 'url';
	const ATTR_TECH_NAME = 'tech_name';

	//константы источников
	/** Сайт e-liquid-recipes */
	const E_LIQUID_RECIPES_ID = 1;
	/** Сайт vape craft */
	const VAPE_CRAFT_ID       = 2;
}