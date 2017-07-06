import config from "../config";
import $ from "jquery";
import RequestPromise from "../models/RequestPromise";
import Constants from "../constants/Constants";
const LOAD_URL = '/recipes/base-data/get-flavors/';

/**
 * Провайдер для получения ароматизаторов.
 */
class FlavorProvider {

	constructor() {
		this.searchCache = [];
	}

	/**
	 * Поиск ароматизаторов по запросу.
	 *
	 * @param {FlavorRequest} request  Строка поиска
	 * @param {function}      callback Коллбэк окончания поиска
	 */
	search (request, callback) {
		let promise = new RequestPromise(Constants.LOAD_STATUS_IN_PROCESS);
		callback(promise);

		$.ajax({
			url: config.baseUrl + LOAD_URL,
			dataType: 'json',
			crossDomain: true,
			data: request,
			success: (response)  => {
				let promise = new RequestPromise(Constants.LOAD_STATUS_FINISHED);
				if (response.result === true) {
					let flavors = response.data.flavors;/** @param {FlavorsResponse} promise */

					let flavorsMap = new Map();

					flavors.forEach(function(flavor) {
						flavorsMap.set(flavor.id, flavor);
					});

					callback(promise, flavorsMap);
					// this.searchCache[query] = response.data.flavors;
				}
			},
			error: function() {
				let promise = new RequestPromise(Constants.LOAD_STATUS_ERROR);
				callback(promise);
			}
		});
	}
}

export default new FlavorProvider();