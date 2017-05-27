<?php

namespace tests\unit;

use _generated\UnitTesterActions;
use Codeception\Test\Unit;
use common\modules\recipes\components\RecipesSaver;
use common\modules\recipes\models\FlavorBrand;
use common\modules\recipes\models\parsing\FlavorModel;
use common\modules\recipes\models\parsing\RecipeModel;
use common\modules\recipes\models\Recipe;
use common\modules\recipes\models\Source;
use tests\unit\fixtures\FlavorBrandFixture;
use tests\unit\fixtures\FlavorFixture;
use tests\unit\fixtures\FlavorSourceLinkFixture;
use tests\unit\fixtures\RecipeFixture;
use tests\unit\fixtures\RecipeFlavorFixture;
use tests\unit\fixtures\SourceFixture;
use Yii;

/**
 * @property UnitTesterActions $tester
 */
class RecipesSaverTest extends Unit {

	public function _before() {
		parent::_before();

		$this->tester->haveFixtures([
			FlavorBrandFixture::class,
			FlavorFixture::class,
			FlavorSourceLinkFixture::class,
			RecipeFixture::class,
			RecipeFlavorFixture::class,
			SourceFixture::class,
		]);
	}

	public function testRecipe1() {
		$inputRecipesData = require(Yii::getAlias('@tests/_data/test-data/input-recipes.php'));

		$recipeRow = $inputRecipesData[0];

		$inputRecipe = new RecipeModel();

		$inputRecipe->load($recipeRow, '');

		foreach ($recipeRow[RecipeModel::ATTR_FLAVORS] as $flavorRow) {
			$flavor = new FlavorModel();

			$flavor->load($flavorRow, '');

			$inputRecipe->flavors[] = $flavor;
		}

		$saver = new RecipesSaver(Source::E_LIQUID_RECIPES_ID);

		$result = $saver->save($inputRecipe);

		$this->assertTrue($result, 'Результат сохранения неуспешный');

		$savedRecipe = Recipe::findOne([
			Recipe::ATTR_SOURCE_ID        => Source::E_LIQUID_RECIPES_ID,
			Recipe::ATTR_SOURCE_RECIPE_ID => $inputRecipe->id,
		]);

		$this->assertNotNull($savedRecipe, 'Сохранённый рецепт в базе не найден');
		$this->assertEquals($inputRecipe->id, $savedRecipe->source_recipe_id, 'Идентификатор не соответствует');
		$this->assertEquals($inputRecipe->title, $savedRecipe->title, 'Название не соответствует');
		$this->assertEquals($inputRecipe->notes, $savedRecipe->notes, 'Описание не соответствует');

		$this->assertCount(count($inputRecipe->flavors), $savedRecipe->flavorLinks, 'Количество аром неверное');

		$createdBrands = FlavorBrand::find()->count();

		$this->assertEquals(2, $createdBrands, 'Количество созданных брендов неверное');
	}

	public function testRecipe2() {
		$inputRecipesData = require(Yii::getAlias('@tests/_data/test-data/input-recipes.php'));

		$recipeRow = $inputRecipesData[1];

		$inputRecipe = new RecipeModel();

		$inputRecipe->load($recipeRow, '');

		foreach ($recipeRow[RecipeModel::ATTR_FLAVORS] as $flavorRow) {
			$flavor = new FlavorModel();

			$flavor->load($flavorRow, '');

			$inputRecipe->flavors[] = $flavor;
		}

		$saver = new RecipesSaver(Source::E_LIQUID_RECIPES_ID);

		$result = $saver->save($inputRecipe);

		$this->assertTrue($result, 'Результат сохранения неуспешный');

		$savedRecipe = Recipe::findOne([
			Recipe::ATTR_SOURCE_ID        => Source::E_LIQUID_RECIPES_ID,
			Recipe::ATTR_SOURCE_RECIPE_ID => $inputRecipe->id,
		]);

		$this->assertNotNull($savedRecipe, 'Сохранённый рецепт в базе не найден');
		$this->assertEquals($inputRecipe->id, $savedRecipe->source_recipe_id, 'Идентификатор не соответствует');
		$this->assertEquals($inputRecipe->title, $savedRecipe->title, 'Название не соответствует');
		$this->assertEquals($inputRecipe->notes, $savedRecipe->notes, 'Описание не соответствует');

		$this->assertCount(count($inputRecipe->flavors), $savedRecipe->flavorLinks, 'Количество аром неверное');
	}

}