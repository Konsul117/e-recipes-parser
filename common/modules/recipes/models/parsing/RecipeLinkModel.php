<?php

namespace common\modules\recipes\models\parsing;

use yii\base\Model;
use yii\validators\RequiredValidator;
use yii\validators\StringValidator;
use yii\validators\UrlValidator;
use yiiCustom\validators\FilterClearTextValidator;

/**
 * Модель ссылки на рецепт.
 */
class RecipeLinkModel extends Model {

	/** @var string URL ссылки */
	public $url;
	const ATTR_URL = 'url';

	/** @var string Названеи рецепта */
	public $title;
	const ATTR_TITLE = 'title';

	/**
	 * @inheritdoc
	 */
	public function rules() {
		return [
			[static::ATTR_URL,   RequiredValidator::class],
			[static::ATTR_URL,   UrlValidator::class],
			[static::ATTR_TITLE, RequiredValidator::class],
			[static::ATTR_TITLE, StringValidator::class],
			[static::ATTR_TITLE, FilterClearTextValidator::class],
		];
	}
}