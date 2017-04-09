<?php

namespace common\modules\recipes\components;

use common\modules\recipes\models\Flavor;
use common\modules\recipes\models\FlavorBrand;
use common\modules\recipes\models\FlavorSourceLink;
use common\modules\recipes\models\Recipe;
use common\modules\recipes\models\RecipeFlavor;
use common\modules\recipes\models\Source;
use phpQuery;
use phpQueryObject;
use yii\base\Exception;
use yiiCustom\logger\LoggerStream;

class VapeCraftGrabber extends AbstractGrabber {

	/** Идентификатор источника */
	const SOURCE_ID = Source::VAPE_CRAFT_ID;

	/** Количество ссылок на рецепты, выводимых на странице */
	const RECIPES_PER_PAGE = 30;

	/** @var string Начальная страница */
	public $startUrl;

	/** @var string[] Бренды ароматизаторов: имя бренда => id */
	protected $flavorsBrandsIdsInKeys;

	/**
	 * @inheritdoc
	 */
	public function start() {
		if ($this->startUrl === null) {
			$this->startUrl = $this->source->url;
		}

		//инициализируем список брендров ароматизаторов
		$this->flavorsBrandsIdsInKeys = FlavorBrand::find()
			->select([FlavorBrand::ATTR_ID])
			->indexBy(FlavorBrand::ATTR_TITLE)
			->column();

		$firstPageHtml = $this->getListPage();

		if ($firstPageHtml === null) {
			$this->logger->log('Не удалось получить первую страницу, прерываем выполнение', LoggerStream::TYPE_ERROR);
		}

		$firstPage = phpQuery::newDocumentHTML($firstPageHtml);/** @var phpQueryObject $firstPage */

		$this->processRecipesLinksPage($firstPage);

		$maxPageNumber = $this->getMaxPageNumber($firstPage);

		for ($pageNumber = 2; $pageNumber <= $maxPageNumber; $pageNumber++) {
			//получаем страницу списка рецептов
			$pageHtml = $this->getListPage($pageNumber);
			$page = phpQuery::newDocumentHTML($pageHtml);/** @var phpQueryObject $page */

			if ($pageHtml === null) {
				$this->logger->log('Не удалось получить страницу, прерываем выполнение', LoggerStream::TYPE_ERROR);

				return ;
			}

			$processResult = $this->processRecipesLinksPage($page);

			if ($processResult === false) {
				$this->logger->log('Произошла ошибка при обработке рецептов', LoggerStream::TYPE_ERROR);

				return ;
			}

			$this->logger->log('Рецепт успешно обработан', LoggerStream::TYPE_ERROR);

			phpQuery::unloadDocuments();
		}
	}

	/**
	 * Получение страницы списка рецептов.
	 *
	 * @param int $pageNumber
	 *
	 * @return string|null
	 */
	protected function getListPage($pageNumber = 1) {
		$url = $this->startUrl . '/?per_page=' . ($pageNumber * static::RECIPES_PER_PAGE);
		$this->logger->log('Получаем страницу ' . $url);

		return $this->load($url);
	}

	/**
	 * Получение списка ссылок на рецепты на странице.
	 *
	 * @param phpQueryObject $page
	 *
	 * @return string[]
	 */
	protected function getRecipesLinks(phpQueryObject $page) {
		$this->logger->log('Парсим ссылки рецептов');

		/** @var string[] $links */
		$links = [];

		foreach ($page->find('.container .tbl_grid') as $tr) {
			$href = phpQuery::pq($tr)->find('.row.r1 .r_name a');

			if ($href->count() === 0) {
				continue;
			}

			$links[] = $href->attr('href');
		}

		$this->logger->log('Получено ссылок: ' . count($links));

		return $links;
	}

	/**
	 * Получение максимального номера страницы пагинации.
	 *
	 * @param phpQueryObject $page
	 *
	 * @return int
	 *
	 * @throws Exception
	 */
	protected function getMaxPageNumber(phpQueryObject $page) {
		$this->logger->log('Получаем максимальный номер страницы');

		$this->logger->log('Получаем максимальный номер страницы');

		$a = $page->find('div.pagination-bar li a:last');
		$href = $a->attr('href');

		if (preg_match('/&per_page=([0-9]+)/i', $href, $result)) {
			$number = $result[1] / static::RECIPES_PER_PAGE;

			$this->logger->log('Результат: ' . $number);
		}
		else {
			throw new Exception('Не удалось получить максимальный номер страницы');
		}

		return $number;
	}

	/**
	 * Обработка страницы ссылок на рецепты.
	 *
	 * @param phpQueryObject $page
	 *
	 * @throws Exception
	 */
	protected function processRecipesLinksPage(phpQueryObject $page) {
		foreach ($this->getRecipesLinks($page) as $recipeUrl) {
			//обрабатываем сам рецепт
			$recipeSiteId = null;

			$fromSourceId = null;
			if (preg_match('/\/main\/recept\/([0-9]+)/i', $recipeUrl, $result)) {
				$fromSourceId = $result[1];
			}

			if ($fromSourceId === null) {
				throw new Exception('Ошибка при парсинге URL рецепта: ' . $recipeUrl);
			}

			$recipePageHtml = $this->load($recipeUrl);

			if ($recipePageHtml === null) {
				throw new Exception('Не удалось загрузить страницу: ' . $recipeUrl);
			}

			$page = phpQuery::newDocumentHTML($recipePageHtml);/** @var phpQueryObject $page */
			$name  = $page->find('#calc-body textarea#title')->val();
			$notes = $page->find('#calc-digits-frame-elem-id2 .notes-block .alert')->text();

			$recipe = Recipe::findOne([
				Recipe::ATTR_SOURCE_ID        => static::SOURCE_ID,
				Recipe::ATTR_SOURCE_RECIPE_ID => $fromSourceId,
			]);/** @var Recipe $recipe */

			if ($recipe === null) {
				$recipe = new Recipe();
				$this->logger->log('Добавляем рецепт ' . $name . ' (' . $recipeUrl . ')');
			}
			else {
				$this->logger->log('Обновляем рецепт ' . $recipe->id . ' ' . $name . ' (' . $recipeUrl . ')');
			}

			$recipe->title            = $name;
			$recipe->notes            = $notes;
			$recipe->source_id        = static::SOURCE_ID;
			$recipe->source_recipe_id = $fromSourceId;

			if ($recipe->save() === false) {
				$this->logger->log('Ошибка при сохранении рецепта: ' . print_r($recipe->errors, true), LoggerStream::TYPE_ERROR);

				return;
			}

			$this->processFlavorsOnRecipePage($recipe->id, $page);
		}
	}

	/**
	 * Обработка ароматизаторов на странцие рецептов.
	 *
	 * @param int $recipeId Идентификатор рецепта
	 * @param phpQueryObject $page Страница
	 */
	protected function processFlavorsOnRecipePage($recipeId, phpQueryObject $page) {
		//обрабатываем ароматизаторы
		//идентификаторы ароматизаторов
		$flavorsSiteIds = [];

		//обрабатываем все ароматизаторы на странице рецепта
		foreach ($page->find('#calc-digits-frame-elem-id2 .calc-flavors-table .calc-flavors-table-flavor') as $flavorLine) {
			$flavorHref = phpQuery::pq($flavorLine)->find('.calc-flavors-row-name a.fl-link');
			$flavorLink = $flavorHref->attr('href');
			$flavorName = $flavorHref->find('.name-label')->text();

			$fromSourceId = null;
			if (preg_match('/\/main\/flavor\/([0-9]+)?/i', $flavorLink, $result)) {
				$fromSourceId = $result[1];
			}

			if ($fromSourceId === null) {
				$this->logger->log('Ошибка при парсинге URL ароматизатора: ' . $flavorHref, LoggerStream::TYPE_ERROR);

				return;
			}

			$flavor = Flavor::find()
				->joinWith(Flavor::REL_SOURCE_LINKS)
				->where([
					FlavorSourceLink::tableName() . '.' . FlavorSourceLink::ATTR_SOURCE_ID        => static::SOURCE_ID,
					FlavorSourceLink::tableName() . '.' . FlavorSourceLink::ATTR_SOURCE_FLAVOR_ID => $fromSourceId,
				])
				->one();/** @var Flavor $flavor */

			$isNew = false;
			if ($flavor === null) {
				$isNew = true;
				$this->logger->log('Добавляем ароматизатор ' . $flavorName . ' (' . $flavorLink . ')');
				$flavor = new Flavor();
			}

			if ($isNew === true || $this->isNeedToUpdateFlavors === true) {
				if ($isNew === true) {
					$flavor = new Flavor();
					$this->logger->log('Добавляем ароматизатор ' . $flavorName . ' (' . $flavorLink . ')');
				}
				else {
					$this->logger->log('Обновляем ароматизатор ' . $flavorName . ' (' . $flavorLink . ')');
				}

				$flavor->title = $flavorName;

				//добавляем бренд
				$brandName = $flavorHref->find('.make-label')->text();

				$brandId = null;

				if (array_key_exists($brandName, $this->flavorsBrandsIdsInKeys) === false) {
					$flavorBrand = new FlavorBrand();

					$flavorBrand->title = $brandName;

					if ($flavorBrand->save() === true) {
						$this->flavorsBrandsIdsInKeys[$brandName] = $flavorBrand->id;

						$brandId = $flavorBrand->id;
					}
					else {
						$this->logger->log('Ошибка при сохранении бренда ароматизатора: ' . print_r($flavorBrand->errors, true), LoggerStream::TYPE_ERROR);
					}
				}
				else {
					$brandId = $this->flavorsBrandsIdsInKeys[$brandName];
				}

				if ($brandId !== null) {
					$flavor->brand_id = $brandId;

					if ($flavor->save() === false) {
						$this->logger->log('Ошибка при сохранении ароматизатора: ' . print_r($flavor->errors, true),
							LoggerStream::TYPE_ERROR);

						return;
					}
				}

				if ($isNew === true) {
					//добавляем связку с источником
					$link = new FlavorSourceLink();

					$link->flavor_id        = $flavor->id;
					$link->source_id        = static::SOURCE_ID;
					$link->source_flavor_id = $fromSourceId;

					if ($link->save() === false) {
						$this->logger->log('Ошибка при сохранении связки ароматизатора с источником: ' . print_r($link->errors,
								true), LoggerStream::TYPE_ERROR);

						return;
					}
				}

			}

			$flavorsSiteIds[] = $flavor->id;
		}

		//далее обновляем список аром у рецепта
		$currentFlavors = RecipeFlavor::find()
			->where([
				RecipeFlavor::ATTR_RECIPE_ID => $recipeId,
			])
			->select(RecipeFlavor::ATTR_FLAVOR_ID)
			->column();

		$newFlavors = array_diff($flavorsSiteIds, $currentFlavors);
		$oldFlavors = array_diff($currentFlavors, $flavorsSiteIds);

		//добавляем новые
		foreach ($newFlavors as $flavorId) {
			$recipeFlavor = new RecipeFlavor();

			$recipeFlavor->recipe_id = $recipeId;
			$recipeFlavor->flavor_id = $flavorId;

			if ($recipeFlavor->save() === false) {
				$this->logger->log('Ошибка при сохранении связи между рецептом и ароматизатором: ' . print_r($recipeFlavor->errors, true), LoggerStream::TYPE_ERROR);

				return;
			}
		}

		//и удаляем старые
		foreach ($oldFlavors as $flavorId) {
			RecipeFlavor::deleteAll([
				RecipeFlavor::ATTR_RECIPE_ID => $recipeId,
				RecipeFlavor::ATTR_FLAVOR_ID => $flavorId
			]);
		}
	}
}