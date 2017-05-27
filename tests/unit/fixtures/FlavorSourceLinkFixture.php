<?php

namespace tests\unit\fixtures;

use common\modules\recipes\models\FlavorSourceLink;
use yii\test\ActiveFixture;

class FlavorSourceLinkFixture extends ActiveFixture {

	public $dataFile = '@tests/_data/tables/flavor-source-link.php';

	public $modelClass = FlavorSourceLink::class;

}