import md5 from "md5";

class FlavorFiltersModel {
	constructor() {
		this.searchQuery = '';
		this.brandsIds  = [];
		this.sourcesIds = [];
	}

	/**
	 * Вычисление хэша настроек фильтров.
	 *
	 * @return {string}
	 */
	calcHash() {
		let string = this.searchQuery;

		this.brandsIds.forEach(function(val) {
			string += val;
		});

		this.sourcesIds.forEach(function(val) {
			string += val;
		});

		return md5(string);
	}
}

export default FlavorFiltersModel;