<?php

namespace common\modules\recipes\components\crawlers;

use common\modules\recipes\components\downloadProvider\DownloadProviderInterface;
use common\modules\recipes\components\downloadProvider\LoadedPage;
use common\modules\recipes\components\parsers\RecipesListPageParserInterface;
use common\modules\recipes\components\RecipesLogger;
use common\modules\recipes\models\parsing\RecipesPageModel;
use common\modules\recipes\models\Source;
use yii\base\Exception;

/**
 * Суперкрасс кроулера.
 */

abstract class AbstractCrawler {
	/** @var int Номер страницы, с которой следует начать кроулинг */
	public $startPageNumber;

	/** @var DownloadProviderInterface Провайдер загрузки страниц */
	protected $downloader;

	/** @var callable[] Коллбэки при получении страницы рецепта */
	protected $callbacksOnRecipePage = [];

	/** @var callable[] Коллбэки при получении страницы списка рецептов */
	protected $callbacksOnRecipesListPage = [];

	/** @var Source Модель источника */
	protected $source;

	/** @var RecipesListPageParserInterface Парсер страниц списков рецептов */
	protected $listPageParser;

	/**
	 * @param DownloadProviderInterface $downloader      Провайдер загрузки страниц
	 * @param int                       $startPageNumber Номер страницы, с которой следует начать кроулинг
	 */
	public function __construct(DownloadProviderInterface $downloader, $startPageNumber = 1) {
		$this->downloader      = $downloader;
		$this->startPageNumber = $startPageNumber;

		$this->init();
	}

	/**
	 * Инициализация компонента.
	 *
	 * @throws Exception
	 */
	protected function init() {
		$this->source = Source::findOne($this->getSourceId());

		if ($this->source === null) {
			throw new Exception('Источник ' . $this->getSourceId() . ' не найден');
		}

		$this->listPageParser = $this->getListParser();
	}

	/**
	 * Добавление коллбэка, вызываемого при получении страницы рецепта.
	 *
	 * @param callable $callback Функция-коллбэк, вызываемая при наступлении события. В аргументах будет передан объект LoadedPage
	 *                           Если функция вернёт false, то кроулинг будет остановлен
	 *
	 * @return static
	 */
	public function onRecipePage(callable $callback) {
		$this->callbacksOnRecipePage[] = $callback;

		return $this;
	}

	/**
	 * Запуск коллбэков при получении страницы рецепта.
	 *
	 * @param LoadedPage       $page      Страница с рецептом
	 *
	 * @return bool Успешность выполнения коллбэков. Если возвращён false, то процесс нужно остановить
	 */
	protected function invokeOnRecipePageCallbacks(LoadedPage $page) {
		foreach ($this->callbacksOnRecipePage as $callback) {
			$result = $callback($page);

			if ($result === false) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Добавление коллбэка, вызываемого при получении страницы списка рецептов.
	 *
	 * @param callable $callback Функция-коллбэк, вызываемая при наступлении события. В аргументах будет передан объект LoadedPage и RecipesPageModel
	 *                           Если функция вернёт false, то кроулинг будет остановлен
	 *
	 * @return static
	 */
	public function onRecipesListPage(callable $callback) {
		$this->callbacksOnRecipesListPage[] = $callback;

		return $this;
	}

	/**
	 * Запуск коллбэков при получении страницы списка рецептов.
	 *
	 * @param LoadedPage       $page      Страница со списком рецептов
	 * @param RecipesPageModel $pageModel Модель спарсенной страницы
	 *
	 * @return bool Успешность выполнения коллбэков. Если возвращён false, то процесс нужно остановить
	 */
	protected function invokeOnRecipesListPageCallbacks(LoadedPage $page, RecipesPageModel $pageModel) {
		foreach ($this->callbacksOnRecipesListPage as $callback) {
			$result = $callback($page, $pageModel);

			if ($result === false) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Запуск процесса кроулинга.
	 *
	 * @return bool Общий результат успешности процесса
	 */
	public function start() {
		RecipesLogger::add('Получение первой страницы');
		$firstPage = $this->downloader->load($this->getListPageUrlByNumber($this->startPageNumber));

		if ($firstPage->isLoadedSuccess === false) {
			RecipesLogger::add('Страницу получить не удалось');

			return false;
		}

		$recipesList = $this->listPageParser->parse($firstPage);
		$recipesList->currentPageNumber = $this->startPageNumber;

		if ($this->invokeOnRecipesListPageCallbacks($firstPage, $recipesList) === false) {
			RecipesLogger::add('Коллбэк обработки страницы списка рецептов вернул false');

			return false;
		}

		$maxPageNumber = $recipesList->maxPagesCount;
		RecipesLogger::add('Общее количество страниц: ' . $maxPageNumber);

		if ($this->processRecipesLinks($recipesList) === false) {
			RecipesLogger::add('Не удалось обработать страницу списка: ' . $firstPage->url);

			return false;
		}

		for ($pageNumber = ($this->startPageNumber + 1); $pageNumber <= $maxPageNumber; $pageNumber++) {
			RecipesLogger::add('Получаем страницу номер: ' . $pageNumber);
			$url = $this->getListPageUrlByNumber($pageNumber);
			$page = $this->downloader->load($url);

			if ($page->isLoadedSuccess === false) {
				RecipesLogger::add('Страницу получить не удалось, номер: ' . $pageNumber . ', url: ' . $url);

				return false;
			}

			$recipesList = $this->listPageParser->parse($page);
			$recipesList->currentPageNumber = $pageNumber;

			if ($this->invokeOnRecipesListPageCallbacks($page, $recipesList) === false) {
				RecipesLogger::add('Коллбэк обработки страницы списка рецептов вернул false');

				return false;
			}

			if ($this->processRecipesLinks($recipesList) === false) {
				RecipesLogger::add('Не удалось обработать страницу списка: ' . $page->url);

				return false;
			}
		}

		return true;
	}

	/**
	 * Обработка списка ссылок на рецепты.
	 *
	 * @param RecipesPageModel $recipesPageModel Модель списка рецептов страницы
	 *
	 * @return bool Успешность выполнения
	 */
	protected function processRecipesLinks(RecipesPageModel $recipesPageModel) {
		foreach ($recipesPageModel->recipeLinks as $link) {
			$recipePage = $this->downloader->load($link->url);

			if ($recipePage->isLoadedSuccess === false) {
				RecipesLogger::add('Не удалось получить страницу рецепта: ' . $link->url);

				return false;
			}

			if ($this->invokeOnRecipePageCallbacks($recipePage) === false) {
				RecipesLogger::add('Коллбэк обработки страницы рецепта вернул false');

				return false;
			}
		}

		return true;
	}

	/**
	 * Получение идентификатора источника.
	 *
	 * @return int
	 */
	abstract protected function getSourceId();

	/**
	 * Получение парсера страницы списка рецептов.
	 *
	 * @return RecipesListPageParserInterface
	 */
	abstract protected function getListParser();

	/**
	 * Получение URL страницы списка по её номеру.
	 *
	 * @param int $number Номер страницы
	 *
	 * @return string
	 */
	abstract protected function getListPageUrlByNumber($number);

}