import Constants from "../constants/Constants";
import AppDispatcher from "../dispatchers/AppDispatcher";
import $ from "jquery";
import config from "../config";

class RecipesActions {
	/**
	 * Поиск рецептов.
	 *
	 * @param {RecipesSearchRequest} params Модель запроса поиска рецептов
	 */
	findRecipes(params) {
		AppDispatcher.dispatch({
			type: Constants.RECIPES_LOADING
		});
		$.ajax({
			url:         config.baseUrl+config.findRecipesUrl,
			data:        params,
			dataType:    'json',
			crossDomain: true,
			success:     (response) => {/** @param {RecipesResponse} response */
				if (response.result === true) {
					AppDispatcher.dispatch({
						type: Constants.RECIPES_LOAD_SUCCESS,
						data: response.data
					});
				}
				else {
					AppDispatcher.dispatch({
						type: Constants.RECIPES_LOAD_FAIL,
					});
				}
			},
			error:       function() {
				AppDispatcher.dispatch({
					type: Constants.RECIPES_LOAD_FAIL,
				});
			}
		});
	}
}

export default new RecipesActions();