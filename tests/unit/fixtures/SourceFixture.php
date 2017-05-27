<?php

namespace tests\unit\fixtures;

use common\modules\recipes\models\Source;
use yii\test\ActiveFixture;

class SourceFixture extends ActiveFixture {

	public $dataFile = '@tests/_data/tables/source.php';

	public $modelClass = Source::class;
}