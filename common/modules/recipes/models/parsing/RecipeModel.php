<?php

namespace common\modules\recipes\models\parsing;

use yii\base\Model;
use yii\validators\RequiredValidator;
use yii\validators\StringValidator;
use yiiCustom\validators\FilterClearTextValidator;

/**
 * Модель страницы рецепта.
 */
class RecipeModel extends Model {

	/** @var string Идентификатор */
	public $id;
	const ATTR_ID = 'id';

	/** @var string Название */
	public $title;
	const ATTR_TITLE = 'title';

	/** @var string Заметки  */
	public $notes;
	const ATTR_NOTES = 'notes';

	/** @var FlavorModel[] Ароматизаторы  */
	public $flavors = [];
	const ATTR_FLAVORS = 'flavors';

	/** @var bool Парсинг выполнен успешно */
	public $isSuccess = false;

	/**
	 * @inheritdoc
	 */
	public function rules() {
		return [
			[static::ATTR_ID,    RequiredValidator::class],
			[static::ATTR_ID,    FilterClearTextValidator::class],
			[static::ATTR_TITLE, RequiredValidator::class],
			[static::ATTR_TITLE, StringValidator::class],
			[static::ATTR_TITLE, FilterClearTextValidator::class],
			[static::ATTR_NOTES, StringValidator::class],
			[static::ATTR_NOTES, FilterClearTextValidator::class],
		];
	}
}