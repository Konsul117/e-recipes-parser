<?php

namespace tests\unit\fixtures;

use common\modules\recipes\models\Recipe;
use yii\test\ActiveFixture;

class RecipeFixture extends ActiveFixture {

	public $dataFile = '@tests/_data/tables/recipe.php';

	public $modelClass = Recipe::class;
}