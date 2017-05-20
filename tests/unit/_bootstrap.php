<?php
use yiiCustom\core\ConfigCollector;

define('YII_ENV', 'test');

$repository = dirname(dirname(__DIR__));

require(__DIR__ . '/../../vendor/autoload.php');
require(__DIR__ . '/../../vendor/yiisoft/yii2/Yii.php');
require($repository . '/common/config/bootstrap.php');

$config = ConfigCollector::getApplicationConfig();

$testConfig = require('config.php');

$config = \yii\helpers\ArrayHelper::merge($config, $testConfig);

$application = new yii\console\Application( $config );