<?php

namespace common\modules\recipes\console\controllers;

use common\components\EmptyLogger;
use common\modules\recipes\components\AbstractGrabber;
use yii\console\Controller;
use yiiCustom\logger\StdoutLogger;
use Yii;

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
		Yii::setLogger(new EmptyLogger());
		$logStream = new StdoutLogger();
		$logStream->memoryUsageOut = false;

		$grabber = AbstractGrabber::getGrabber($sourceId, $logStream);

		if ($grabber === null) {
			$this->stdout('Ошибка инициализации граббера. Возможно, источник некорректный' . PHP_EOL);

			return;
		}

//		$grabber->isNeedToUpdateFlavors = true;
		$grabber->useProxy = true;

		$grabber->start();
	}
}