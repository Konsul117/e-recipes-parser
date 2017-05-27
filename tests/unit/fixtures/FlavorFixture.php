<?php

namespace tests\unit\fixtures;

use common\modules\recipes\models\Flavor;
use yii\test\ActiveFixture;

class FlavorFixture extends ActiveFixture {

	public $dataFile = '@tests/_data/tables/flavor.php';

	public $modelClass = Flavor::class;
}