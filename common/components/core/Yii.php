<?php

use common\components\core\ModuleManager;
use yiiCustom\core\ConfigManager;
use yiiCustom\core\Environment;
use yiiCustom\core\View;

class Yii extends \yii\BaseYii {
	/** @var yii\console\Application|yii\web\Application|Application The application instance */
	public static $app;
}

/**
 * @property-read ConfigManager $configManager
 * @property-read View          $view
 * @property-read ModuleManager $moduleManager
 * @property-read Environment   $env
 */
class Application {}
