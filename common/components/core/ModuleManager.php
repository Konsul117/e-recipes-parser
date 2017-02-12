<?php
namespace common\components\core;

use yii\base\BootstrapInterface;

/**
 * Компонент для управления и проверки модулей.
 *
 * @property-read ModuleManagerModules $modules Провайдер включённых модулей
 */
class ModuleManager extends \yiiCustom\core\ModuleManager  implements BootstrapInterface {}