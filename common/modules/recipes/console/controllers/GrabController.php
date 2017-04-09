<?php

namespace common\modules\recipes\console\controllers;

use common\modules\recipes\components\AbstractGrabber;
use Yii;
use yii\console\Controller;
use yiiCustom\logger\StdoutLogger;

/**
 * Контроллер граббинга.
 */
class GrabController extends Controller {

	/**
	 * Начать граббинг.
	 *
	 * @param int $sourceId Идентификатор источника
	 */
	public function actionIndex($sourceId) {
		$logStream = new StdoutLogger();
		$logStream->memoryUsageOut = false;

		$grabber = AbstractGrabber::getGrabber($sourceId, $logStream, Yii::$app->moduleManager->modules->recipes->proxyProvider);

		if ($grabber === null) {
			$this->stdout('Ошибка инициализации граббера. Возможно, источник некорректный' . PHP_EOL);

			return;
		}

		$grabber->isNeedToUpdateFlavors = true;
		$grabber->useProxy = true;

		$grabber->start();
	}
}