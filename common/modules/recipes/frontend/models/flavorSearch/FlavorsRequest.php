<?php

namespace common\modules\recipes\frontend\models\flavorSearch;
use common\modules\recipes\models\Flavor;
use common\modules\recipes\models\FlavorSourceLink;
use yii\base\Model;
use yii\validators\EachValidator;
use yii\validators\NumberValidator;
use yiiCustom\validators\FilterClearTextValidator;
use yiiCustom\validators\ReferenceValidator;

/**
 * Запрос получения ароматизаторов.
 */
class FlavorsRequest extends Model {

	/** Лимит поиска по умолчанию */
	const DEFAULT_SEARCH_LIMIT = 20;

	/** @var string Строка запроса поиска */
	public $nameQuery;
	const ATTR_NAME_QUERY = 'nameQuery';

	/** @var int[] Идентификатры брендов */
	public $brandsIds = [];
	const ATTR_BRANDS_IDS = 'brandsIds';

	/** @var int[] Идентификатор источника */
	public $sourcesIds = [];
	const ATTR_SOURCES_IDS = 'sourcesIds';

	/** @var int Лимит поиска */
	public $limit = self::DEFAULT_SEARCH_LIMIT;
	const ATTR_LIMIT = 'limit';

	/**
	 * @inheritdoc
	 */
	public function rules() {
		return [
			[static::ATTR_NAME_QUERY,  FilterClearTextValidator::class],
			[static::ATTR_BRANDS_IDS,  EachValidator::class, 'rule' => [ReferenceValidator::class]],
			[static::ATTR_SOURCES_IDS, EachValidator::class, 'rule' => [ReferenceValidator::class]],
			[static::ATTR_LIMIT,       NumberValidator::class, 'integerOnly' => true, 'min' => 1, 'max' => static::DEFAULT_SEARCH_LIMIT],
		];
	}

	/**
	 * Выполнение поиска
	 *
	 * @return FlavorsResponse
	 */
	public function search() {
		$response = new FlavorsResponse();

		$query = Flavor::find()
			->innerJoinWith(Flavor::REL_SOURCE_LINKS);

		$searchQuery = $this->nameQuery;
		$searchQuery = trim($searchQuery);
		$searchQuery = str_replace(' ', '%', $searchQuery);

		//учитываем фильтры
		if ($this->nameQuery !== null && $this->nameQuery !== '') {
			$query->where(Flavor::ATTR_TITLE . ' LIKE \'%' . $searchQuery . '%\'');//todo Проверить регистроненависимость
		}

		if (count($this->brandsIds) > 0) {
			$query->andWhere([Flavor::tableName() . '.' . Flavor::ATTR_BRAND_ID => $this->brandsIds]);
		}

		if (count($this->sourcesIds) > 0) {
			$query->andWhere([FlavorSourceLink::tableName() . '.' . FlavorSourceLink::ATTR_SOURCE_ID => $this->sourcesIds]);
		}

		$totalCount = $query->count();

		$response->totalCount = $totalCount;

		if ($totalCount > 0) {
			$data = $query->limit($this->limit)
				->all();/** @var Flavor[] $data */

			foreach ($data as $item) {
				$flavorItem = new FlavorItemResponse();

				$flavorItem->id      = $item->id;
				$flavorItem->name    = $item->title;
				$flavorItem->brandId = $item->brand_id;

				foreach($item->sourceLinks as $link) {
					$flavorItem->sourcesIds[] = $link->source_id;
				}

				$response->flavors[] = $flavorItem;
			}
		}

		return $response;
	}
}