<?php

namespace common\modules\recipes\components\crawlers;

use common\modules\recipes\components\parsers\eLiquidRecipes\ELiquidRecipesListPageParser;
use common\modules\recipes\models\Source;

/**
 * Кроулер для сайта e-liquid-recipes.com.
 */
class ELiquidRecipesCrawler extends AbstractCrawler {

	/**
	 * @inheritdoc
	 */
	protected function getListPageUrlByNumber($number) {
		if ($number === 1) {
			return $this->source->url;
		}

		return $this->source->url . '/?page=' . $number;
	}

	/**
	 * @inheritdoc
	 */
	protected function getSourceId() {
		return Source::E_LIQUID_RECIPES_ID;
	}

	/**
	 * @inheritdoc
	 */
	protected function getListParser() {
		return new ELiquidRecipesListPageParser();
	}
}