<?php

defined('YII_ENV') || define('YII_ENV', 'test');

$repository = dirname(dirname(__DIR__));
//
require(__DIR__ . '/../../vendor/autoload.php');
require(__DIR__ . '/../../vendor/yiisoft/yii2/Yii.php');
require($repository . '/common/config/bootstrap.php');
//
//$config = ConfigCollector::getApplicationConfig();
//
//$application = new yii\console\Application($config);