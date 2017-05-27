<?php

namespace common\modules\recipes\components;
use Yii;

/**
 * Обёртка для логгирования процессов парсинга.
 * Нужна для удобного использования, просто перенаправляет сообщения в Yii-евский логгер.
 */
class RecipesLogger {

	/**
	 * Добавление сообщения в лог.
	 *
	 * @param string $message Сообщение
	 */
	public static function add($message) {
		Yii::info($message, 'recipes' . (Yii::$app->moduleManager->modules->recipes->currentSourceId ? Yii::$app->moduleManager->modules->recipes->currentSourceId : ''));
	}

}