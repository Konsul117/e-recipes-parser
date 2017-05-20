<?php

namespace tests\unit;
use common\modules\recipes\components\downloadProvider\LoadedPage;
use common\modules\recipes\components\parsers\vapeCraft\VapeCraftRecipePageParser;
use common\modules\recipes\components\parsers\vapeCraft\VapeCraftRecipesListPageParser;
use Yii;

/**
 * Тестирование парсера Vapecraft для страниц ссылок рецептов.
 */
class VapeCraftParserTest extends \Codeception\Test\Unit {

	const PAGES_PATH = '@tests/_data/sites-pages/vape-craft';

	/**
	 * Тестирование страницы со списком ссылок на рецепты.
	 */
	public function testRecipesPage() {
		$pageContent = file_get_contents(Yii::getAlias(static::PAGES_PATH) . DIRECTORY_SEPARATOR . 'recipes-list_1.html');

		$page = new LoadedPage();

		$page->url  = 'http://www.vapecraft.ru/';
		$page->body = $pageContent;

		$parser = new VapeCraftRecipesListPageParser();

		$result = $parser->parse($page);

		$this->assertTrue($result->isSuccess, 'Парсинг завершился ошибкой');

		//проверяем общее количество рецептов в списке
		$this->assertEquals(30, count($result->recipeLinks), 'Количество рецептов должно быть 30');

		//проверяем названия некоторых рецептов
		$this->assertEquals('Жвачка из редбула', $result->recipeLinks[0]->title, 'Название не соответствует');
		$this->assertEquals('вкусная табачка', $result->recipeLinks[12]->title, 'Название не соответствует');
		$this->assertEquals('Unicorn porn (клон 99)', $result->recipeLinks[29]->title, 'Название не соответствует');
	}

	/**
	 * Тестирование страницы рецептов №1
	 */
	public function testRecipePage1() {
		$pageContent = file_get_contents(Yii::getAlias(static::PAGES_PATH) . DIRECTORY_SEPARATOR . 'recipe_1.html');

		$page = new LoadedPage();

		$page->url  = 'http://www.vapecraft.ru/main/recept/38088';
		$page->body = $pageContent;

		$parser = new VapeCraftRecipePageParser();

		$result = $parser->parse($page);

		$this->assertTrue($result->isSuccess, 'Парсинг завершился ошибкой');

		$this->assertEquals($result->id, 38088, 'Id не совпадает');
		$this->assertEquals($result->title, 'Nicoticket Betelgeuse', 'Название не совпадает');
		$this->assertEquals(count($result->flavors), 4, 'Количество аром не совпадает');

		//проверка концентраций
		$this->assertEquals($result->flavors[0]->content, 0.07,   'Арома #1: концертрация неверна');
		$this->assertEquals($result->flavors[1]->content, 0.04,   'Арома #2: концертрация неверна');
		$this->assertEquals($result->flavors[2]->content, 0.04,  'Арома #3: концертрация неверна');
		$this->assertEquals($result->flavors[3]->content, 0.03,  'Арома #4: концертрация неверна');

		//проверка идентификаторов аром
		$this->assertEquals($result->flavors[0]->id, 6768, 'Арома #1: id неверен');
		$this->assertEquals($result->flavors[1]->id, 6537,   'Арома #2: id неверен');
		$this->assertEquals($result->flavors[2]->id, 280,   'Арома #3: id неверен');
		$this->assertEquals($result->flavors[3]->id, 290,  'Арома #4: id неверен');
	}

	/**
	 * Тестирование страницы рецептов №2
	 */
	public function testRecipePage2() {
		$pageContent = file_get_contents(Yii::getAlias(static::PAGES_PATH) . DIRECTORY_SEPARATOR . 'recipe_2.html');

		$page = new LoadedPage();

		$page->url  = 'http://www.vapecraft.ru/main/recept/38587';
		$page->body = $pageContent;

		$parser = new VapeCraftRecipePageParser();

		$result = $parser->parse($page);

		$this->assertTrue($result->isSuccess, 'Парсинг завершился ошибкой');

		$this->assertEquals($result->id, 38587, 'Id не совпадает');
		$this->assertEquals($result->title, 'Ромовая баба', 'Название не совпадает');
		$this->assertEquals(count($result->flavors), 6, 'Количество аром не совпадает');

		//проверка концентраций
		$this->assertEquals($result->flavors[0]->content, 0.07,   'Арома #1: концертрация неверна');
		$this->assertEquals($result->flavors[1]->content, 0.02,   'Арома #2: концертрация неверна');
		$this->assertEquals($result->flavors[2]->content, 0.01,   'Арома #3: концертрация неверна');
		$this->assertEquals($result->flavors[3]->content, 0.01,   'Арома #4: концертрация неверна');
		$this->assertEquals($result->flavors[4]->content, 0.01,   'Арома #5: концертрация неверна');
		$this->assertEquals($result->flavors[5]->content, 0.004,  'Арома #6: концертрация неверна');

		//проверка идентификаторов аром
		$this->assertEquals($result->flavors[0]->id, 6528, 'Арома #1: id неверен');
		$this->assertEquals($result->flavors[1]->id, 6755, 'Арома #2: id неверен');
		$this->assertEquals($result->flavors[2]->id, 6758, 'Арома #3: id неверен');
		$this->assertEquals($result->flavors[3]->id, 6619, 'Арома #4: id неверен');
		$this->assertEquals($result->flavors[4]->id, 565,  'Арома #5: id неверен');
		$this->assertEquals($result->flavors[5]->id, 3748, 'Арома #6: id неверен');
	}
}