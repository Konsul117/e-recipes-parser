<?php

namespace tests\unit\fixtures;

use common\modules\recipes\models\RecipeFlavor;
use yii\test\ActiveFixture;

class RecipeFlavorFixture extends ActiveFixture {

	public $dataFile = '@tests/_data/tables/recipe-flavor.php';

	public $modelClass = RecipeFlavor::class;
}