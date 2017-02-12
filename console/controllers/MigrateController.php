<?php
namespace console\controllers;

use Yii;
use yii\base\Exception;
use yii\helpers\Console;
use GlobIterator;

/**
 * Расширение стандартного мигратора Yii.
 */
class MigrateController extends \yii\console\controllers\MigrateController {
	/** @var string Название таблицы для миграций. */
	public $migrationTable = 'migration';

	/** @var string[] Список всех-всех миграций, которые есть в проекте (см. метод getAllMigrations). */
	private $_allMigrations;

	/**
	 * @inheritdoc
	 */
	protected function createMigration($class) {
		// -- В отличии от базового метода, тут происходит поиск файла миграции среди всех миграций проекта
		$file = array_search($class, $this->_getAllMigrations());
		if ($file === false) {
			throw new Exception('Не найден файл миграции "' . $class . '"');
		}
		require_once($file);
		// -- -- -- --

		return new $class();
	}

	/**
	 * @inheritdoc
	 */
	protected function getNewMigrations() {
		$appliedMigrations = $this->getMigrationHistory(null);

		// -- Проходимся по всем миграциям и проверяем, какие из них ещё не применены
		$newMigrations = [];
		foreach ($this->_getAllMigrations() as $migrationFilename => $migrationVersion) {
			if (false === array_key_exists($migrationVersion, $appliedMigrations)) {
				$newMigrations[$migrationFilename] = $migrationVersion;
			}
		}
		// -- -- -- --

		return $newMigrations;
	}

	/**
	 * Получение списка всех-всех миграций в проекте.
	 *
	 * @return array Ключём является полный путь до файла, значением - название класса миграции
	 */
	private function _getAllMigrations() {
		// -- Если миграции ещё не были получены, получаем их
		if ($this->_allMigrations === null) {
			$migrations = [];

			// -- Сначала ищем основные миграции (безмодульные)
			foreach (new GlobIterator(Yii::$app->basePath . '/migrations/*.php') as $item) {/** @var GlobIterator $item */
				if (1 === preg_match('/^(m(\d{6}_\d{6})_.*?)\.php$/', $item->getFilename(), $matches)) {
					$migrations[$item->getPathname()] = $matches[1];
				}
			}
			// -- -- -- --

			// -- Затем проходимся по каждому модулю и ищем для него миграции
			foreach (new GlobIterator(Yii::$app->configManager->getRepositoryRootPath() . '/common/modules/*') as $moduleItem) {/** @var GlobIterator $moduleItem */
				// -- Пропускаем всё, что не является папками модулей
				if (false === is_dir($moduleItem->getPathname())) {
					continue;
				}
				if ('.' === $moduleItem->getFilename() || '..' === $moduleItem->getFilename()) {
					continue;
				}
				if (false === file_exists($moduleItem->getPathname() . '/migrations')) {
					continue;
				}
				// -- -- -- --

				// -- Проходимся по каждой миграции
				foreach (new GlobIterator($moduleItem->getPathname() . '/migrations/*.php') as $migrationItem) {/** @var GlobIterator $migrationItem */
					if (1 === preg_match('/^(m(\d{6}_\d{6})_.*?)\.php$/', $migrationItem->getFilename(), $matches)) {
						$migrations[$migrationItem->getPathname()] = $matches[1];
					}
				}
				// -- -- -- --
			}
			// -- -- -- --

			asort($migrations);

			$this->_allMigrations = $migrations;
		}
		// -- -- -- --

		return $this->_allMigrations;
	}

	/**
	 * Накат миграций.
	 *
	 * В отличие от стандартного поведения Yii, в нашем методе можно накатить конкретную миграцию.
	 * Достаточно указать её имя (класс).
	 *
	 * @param string|int|null $arg
	 * @return int
	 */
	public function actionUp($arg = null) {
		$migrations = $this->getNewMigrations();

		// -- Если указан лимит или не указано ничего, то запускаем родительский метод
		if (null === $arg) {
			$arg = count($migrations);
		}
		if (is_numeric($arg)) {
			return parent::actionUp($arg);
		}
		// -- -- -- --

		// -- Проверяем, есть ли вообще указанная миграция
		if (false === in_array($arg, $migrations)) {
			$this->stdout("\nMigration not found.\n", Console::FG_RED);
			return self::EXIT_CODE_ERROR;
		}
		// -- -- -- --

		if ($this->confirm('Apply the ' . $arg . '?')) {
			// -- Пытаемся выполнить накат миграции
			if (true !== $this->migrateUp($arg)) {
				$this->stdout("\nMigration failed. The rest of the migrations are canceled.\n", Console::FG_RED);
				return self::EXIT_CODE_ERROR;
			}
			// -- -- -- --

			$this->stdout("\nMigrated up successfully.\n", Console::FG_GREEN);
		}

		return self::EXIT_CODE_NORMAL;
	}

	/**
	 * Откат миграций.
	 *
	 * В отличие от стандартного поведения Yii, в нашем методе можно откатить конкретную миграцию.
	 * Достаточно указать её имя (класс).
	 *
	 * @param string|int|null $arg
	 * @return int
	 */
	public function actionDown($arg = null) {
		// -- Если указан лимит или не указано ничего, то запускаем родительский метод
		if (null === $arg) {
			$arg = 1;
		}
		if (is_numeric($arg)) {
			return parent::actionDown($arg);
		}
		// -- -- -- --

		// -- Проверяем, есть ли вообще указанная миграция
		$migrations = $this->_getAllMigrations();
		if (false === in_array($arg, $migrations)) {
			$this->stdout("\nMigration not found.\n", Console::FG_RED);
			return self::EXIT_CODE_ERROR;
		}
		// -- -- -- --

		if ($this->confirm('Revert the ' . $arg . '?')) {
			// -- Пытаемся выполнить откат миграции
			if (true !== $this->migrateDown($arg)) {
				$this->stdout("\nMigration failed. The rest of the migrations are canceled.\n", Console::FG_RED);
				return self::EXIT_CODE_ERROR;
			}
			// -- -- -- --

			$this->stdout("\nMigrated down successfully.\n", Console::FG_GREEN);
		}

		return self::EXIT_CODE_NORMAL;
	}

	/**
	 * Откат и накат миграций.
	 *
	 * В отличие от стандартного поведения Yii, в нашем методе можно откатить и накатить конкретную миграцию.
	 * Достаточно указать её имя (класс).
	 *
	 * @param string|int|null $arg
	 * @return int
	 */
	public function actionRedo($arg = null) {
		// -- Если указан лимит или не указано ничего, то запускаем родительский метод
		if (null === $arg) {
			$arg = 1;
		}
		if (is_numeric($arg)) {
			return parent::actionDown($arg);
		}
		// -- -- -- --

		// -- Проверяем, есть ли вообще указанная миграция
		$migrations = $this->_getAllMigrations();
		if (false === in_array($arg, $migrations)) {
			$this->stdout("\nMigration not found.\n", Console::FG_RED);
			return self::EXIT_CODE_ERROR;
		}
		// -- -- -- --

		// -- Пытаемся выполнить откат и накат миграции
		if ($this->confirm('Redo the ' . $arg . '?')) {
			if (true !== $this->migrateDown($arg)) {
				$this->stdout("\nMigration failed. The rest of the migrations are canceled.\n", Console::FG_RED);
				return self::EXIT_CODE_ERROR;
			}
			if (true !== $this->migrateUp($arg)) {
				$this->stdout("\nMigration failed. The rest of the migrations migrations are canceled.\n", Console::FG_RED);
				return self::EXIT_CODE_ERROR;
			}

			$this->stdout("\nMigration redone successfully.\n", Console::FG_GREEN);
		}
		// -- -- -- --

		return self::EXIT_CODE_NORMAL;
	}
}