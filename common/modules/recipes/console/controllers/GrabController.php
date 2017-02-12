<?php

namespace common\modules\recipes\console\controllers;

use common\modules\recipes\components\AbstractGrabber;
use common\modules\recipes\components\ELiquidRecipesGrabber;
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

		$grabber = AbstractGrabber::getGrabber($sourceId, $logStream);

		if ($grabber === null) {
			$this->stdout('Ошибка инициализации граббера. Возможно, источник некорректный' . PHP_EOL);

			return;
		}

		$grabber->start();
	}
}