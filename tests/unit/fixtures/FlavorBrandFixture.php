<?php

namespace tests\unit\fixtures;

use common\modules\recipes\models\FlavorBrand;
use yii\test\ActiveFixture;

class FlavorBrandFixture extends ActiveFixture {

	public $dataFile = '@tests/_data/tables/flavor-brand.php';

	public $modelClass = FlavorBrand::class;

}