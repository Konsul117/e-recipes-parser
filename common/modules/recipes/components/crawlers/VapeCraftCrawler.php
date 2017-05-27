<?php

namespace common\modules\recipes\components\crawlers;

use common\modules\recipes\components\parsers\vapeCraft\VapeCraftRecipesListPageParser;
use common\modules\recipes\models\Source;

/**
 * Кроулер для сайта vapecraft.ru.
 */
class VapeCraftCrawler extends AbstractCrawler {

	/** Количество ссылок на рецепты, выводимых на странице */
	const RECIPES_PER_PAGE = 30;

	/**
	 * @inheritdoc
	 */
	protected function getSourceId() {
		return Source::VAPE_CRAFT_ID;
	}

	/**
	 * @inheritdoc
	 */
	protected function getListParser() {
		return new VapeCraftRecipesListPageParser();
	}

	/**
	 * @inheritdoc
	 */
	protected function getListPageUrlByNumber($number) {
		if ($number === 1) {
			return $this->source->url;
		}

		return $this->source->url . '/?per_page=' . ($number * static::RECIPES_PER_PAGE);
	}
}