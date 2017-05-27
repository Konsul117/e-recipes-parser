<?php
use common\modules\recipes\components\downloadProvider\LoadedPage;
use common\modules\recipes\components\parsers\eLiquidRecipes\ELiquidRecipeParser;
use common\modules\recipes\components\parsers\eLiquidRecipes\ELiquidRecipesListPageParser;

/**
 * Тестирование парсера E-Liqid-Recipes для страниц ссылок рецептов.
 */
class ELiquidRecipesParserTest extends \Codeception\Test\Unit {

	const PAGES_PATH = '@tests/_data/sites-pages/e-liquid-recipes';

	/**
	 * Тестирование страницы со списком ссылок на рецепты.
	 */
	public function testRecipesListPage() {
		$pageContent = file_get_contents(Yii::getAlias(static::PAGES_PATH) . DIRECTORY_SEPARATOR . 'recipes-list_1.html');

		$page = new LoadedPage();

		$page->url  = 'http://e-liquid-recipes.com/';
		$page->body = $pageContent;

		$parser = new ELiquidRecipesListPageParser();

		$result = $parser->parse($page);

		$this->assertTrue($result->isSuccess, 'Парсинг завершился ошибкой');

		//проверяем общее количество рецептов в списке
		$this->assertEquals(25, count($result->recipeLinks), 'Количество рецептов должно быть 25');

		//проверяем названия некоторых рецептов
		$this->assertEquals('Go-nutz', $result->recipeLinks[0]->title, 'Название не соответствует');
		$this->assertEquals('Rootbeer Float', $result->recipeLinks[12]->title, 'Название не соответствует');
		$this->assertEquals('coffee toffee', $result->recipeLinks[24]->title, 'Название не соответствует');

	}

	/**
	 * Тестирование страницы рецептов №1
	 */
	public function testRecipePage1() {
		$pageContent = file_get_contents(Yii::getAlias(static::PAGES_PATH) . DIRECTORY_SEPARATOR . 'recipe_1.html');

		$page = new LoadedPage();

		$page->url  = 'http://e-liquid-recipes.com/recipe/1421824/Go-nutz';
		$page->body = $pageContent;

		$parser = new ELiquidRecipeParser();

		$result = $parser->parse($page);

		$this->assertTrue($result->isSuccess, 'Парсинг завершился ошибкой');

		$this->assertEquals($result->id, 1421824, 'Id не совпадает');
		$this->assertEquals($result->title, 'Go-nutz', 'Название не совпадает');
		$this->assertEquals(count($result->flavors), 7, 'Количество аром не совпадает');

		//проверка концентраций
		$this->assertEquals($result->flavors[0]->content, 0.01,   'Арома #1: концертрация неверна');
		$this->assertEquals($result->flavors[1]->content, 0.02,   'Арома #2: концертрация неверна');
		$this->assertEquals($result->flavors[2]->content, 0.015,  'Арома #3: концертрация неверна');
		$this->assertEquals($result->flavors[3]->content, 0.055,  'Арома #4: концертрация неверна');
		$this->assertEquals($result->flavors[4]->content, 0.04,   'Арома #5: концертрация неверна');
		$this->assertEquals($result->flavors[5]->content, 0.005,  'Арома #6: концертрация неверна');
		$this->assertEquals($result->flavors[6]->content, 0.0025, 'Арома #7: концертрация неверна');

		//проверка идентификаторов аром
		$this->assertEquals($result->flavors[0]->id, 172654, 'Арома #1: id неверен');
		$this->assertEquals($result->flavors[1]->id, 7310,   'Арома #2: id неверен');
		$this->assertEquals($result->flavors[2]->id, 2717,   'Арома #3: id неверен');
		$this->assertEquals($result->flavors[3]->id, 17290,  'Арома #4: id неверен');
		$this->assertEquals($result->flavors[4]->id, 100095, 'Арома #5: id неверен');
		$this->assertEquals($result->flavors[5]->id, 626,    'Арома #6: id неверен');
		$this->assertEquals($result->flavors[6]->id, 8756,   'Арома #7: id неверен');
	}

	/**
	 * Тестирование страницы рецептов №2
	 */
	public function testRecipePage2() {
		$pageContent = file_get_contents(Yii::getAlias(static::PAGES_PATH) . DIRECTORY_SEPARATOR . 'recipe_2.html');

		$page = new LoadedPage();

		$page->url  = 'http://e-liquid-recipes.com/recipe/1427611/Creamberry';
		$page->body = $pageContent;

		$parser = new ELiquidRecipeParser();

		$result = $parser->parse($page);

		$this->assertTrue($result->isSuccess, 'Парсинг завершился ошибкой');

		$this->assertEquals($result->id, 1427611, 'Id не совпадает');
		$this->assertEquals($result->title, 'Creamberry', 'Название не совпадает');
		$this->assertEquals(count($result->flavors), 1, 'Количество аром не совпадает');

		//проверка концентраций
		$this->assertEquals($result->flavors[0]->content, 0.1,   'Арома #1: концертрация неверна');

		//проверка идентификаторов аром
		$this->assertEquals($result->flavors[0]->id, 165349, 'Арома #1: id неверен');
	}

	/**
	 * Тестирование страницы рецептов №3
	 */
	public function testRecipePage3() {
		$pageContent = file_get_contents(Yii::getAlias(static::PAGES_PATH) . DIRECTORY_SEPARATOR . 'recipe_3.html');

		$page = new LoadedPage();

		$page->url  = 'http://e-liquid-recipes.com/recipe/1473491/Bubble';
		$page->body = $pageContent;

		$parser = new ELiquidRecipeParser();

		$result = $parser->parse($page);

		$this->assertTrue($result->isSuccess, 'Парсинг завершился ошибкой');

		$this->assertEquals($result->id, 1473491, 'Id не совпадает');
		$this->assertEquals($result->title, 'Bubble', 'Название не совпадает');
		$this->assertEquals(count($result->flavors), 4, 'Количество аром не совпадает');

		//проверка концентраций
		$this->assertEquals($result->flavors[0]->content, 0.01,   'Арома #1: концертрация неверна');

		//проверка идентификаторов аром
		$this->assertEquals($result->flavors[0]->id, 152098, 'Арома #1: id неверен');

		$this->assertEquals($result->flavors[1]->brandTitle, '', 'Название бренда не совпадает (должно быть пустым)');
	}

	/**
	 * Тестирование страницы рецептов №4
	 */
	public function testRecipePage4() {
		$pageContent = file_get_contents(Yii::getAlias(static::PAGES_PATH) . DIRECTORY_SEPARATOR . 'recipe_4.html');

		$page = new LoadedPage();

		$page->url  = 'http://e-liquid-recipes.com/recipe/1473372/*Strawberry%20cream%20-%20ROG*';
		$page->body = $pageContent;

		$parser = new ELiquidRecipeParser();

		$result = $parser->parse($page);

		$this->assertTrue($result->isSuccess, 'Парсинг завершился ошибкой');

		$this->assertTrue($result->validate(), 'Рецепт невалиден: ' . var_export($result->errors, true));

		$this->assertEquals($result->id, 1473372, 'Id не совпадает');
		$this->assertEquals($result->title, '*Strawberry cream - ROG*', 'Название не совпадает');
		$this->assertEquals(count($result->flavors), 5, 'Количество аром не совпадает');

		//проверка концентраций
		$this->assertEquals($result->flavors[0]->content, 0.015,   'Арома #1: концертрация неверна');

		//проверка идентификаторов аром
		$this->assertEquals($result->flavors[0]->id, 153229, 'Арома #1: id неверен');
	}
}