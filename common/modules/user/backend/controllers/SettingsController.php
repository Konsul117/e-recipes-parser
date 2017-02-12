<?php

namespace common\modules\user\backend\controllers;

/**
 * Контроллер настроек модуля User в админке.
 */
class SettingsController extends \yiiCustom\backend\controllers\SettingsController {

	/** @var string Название модуля, настройки которого будут обрабатываться в наследнике контроллера */
	public $moduleName = 'user';
}