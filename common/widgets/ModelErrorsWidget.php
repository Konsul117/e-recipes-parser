<?php

namespace common\widgets;

use yii\base\InvalidConfigException;
use yii\base\Model;
use yiiCustom\core\Widget;

class ModelErrorsWidget extends Widget {

	/** @var Model */
	public $model;
	const ATTR_MODEL = 'model';

	/** @var bool Выводить ошибки вместе с названиями полей */
	public $withFieldsNames = true;
	const ATTR_WITH_FIELDS_NAMES = 'withFieldsNames';

	/**
	 * @inheritdoc
	 *
	 * @throws InvalidConfigException
	 */
	public function run() {
		if ($this->model === null) {
			throw new InvalidConfigException('Не передана модель');
		}

		return $this->render('form-errors-widget', ['widget' => $this]);
	}

}