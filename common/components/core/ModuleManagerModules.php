<?php
namespace common\components\core;

use common\modules\recipes\Recipes;
use common\modules\user\User;

/**
 * @property-read Recipes $recipes           Модуль рецептов
 * @property-read User    $user              Модуль пользователей
 */
class ModuleManagerModules extends \yiiCustom\core\ModuleManagerModules {}