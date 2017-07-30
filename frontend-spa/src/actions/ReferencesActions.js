import Constants from "../constants/Constants";
import AppDispatcher from "../dispatchers/AppDispatcher";
import $ from "jquery";
import config from "../config";

class ReferencesActions {
	loadReferences() {
		$.ajax({
			url:         config.baseUrl+config.referencesLoadUrl,
			dataType:    'json',
			crossDomain: true,
			success:     (response) => {
				let brands = new Map();
				let sources = new Map();
				response.data.brands.forEach(function(val) {
					brands.set(val.id, val);
				});

				response.data.sources.forEach(function(val) {
					sources.set(val.id, val);
				});

				AppDispatcher.dispatch({
					type: Constants.REFERENCES_LOAD_SUCCESS,
					data: {
						brands:  brands,
						sources: sources
					},
				});
			},
			error:       function() {
				AppDispatcher.dispatch({
					type: Constants.REFERENCES_LOAD_FAIL,
				});
			}
		});
	}
}

export default new ReferencesActions();