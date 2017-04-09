import {EventEmitter} from "events";
import AppDispatcher from "../dispatchers/AppDispatcher";
import AppConstants from "../constants/ApplicationConstants";
import config from "../config";
import $ from "jquery";
const LOAD_URL = '/recipes/base-data/get-references/';


let _brands = [];
let _sources = [];
let _isDataLoaded = false;

const EVENT_LOAD = 'load';

const ReferencesStore = Object.assign([], EventEmitter.prototype, {
	getBrandsList() {
		return _brands;
	},

	getSourcesList() {
		return _sources;
	},

	emitLoad() {
		this.emit(EVENT_LOAD);
	},

	addLoadListener(callback) {
		console.log('addLoadListener');
		this.on(EVENT_LOAD, callback);

		//если данные уже загружены, то сразу вызываем коллбэк
		if (_isDataLoaded === true) {
			console.log('уже есть');
			callback();
		}
	},

	removeLoadListener(callback) {
		this.removeListener(EVENT_LOAD, callback);
	}
});

AppDispatcher.register(function(action) {
	switch(action.type) {
		case AppConstants.REFERENCES_LOAD_SUCCESS: {
			console.log('Загрузили');
			_isDataLoaded = true;
			_brands = action.data.brands;
			_sources = action.data.sources;

			ReferencesStore.emitLoad();

			break;
		}

		case AppConstants.REFERENCES_LOAD_FAIL: {
			_isDataLoaded = false;
			_brands = [];

			ReferencesStore.emitLoad();

			break;
		}
	}
});

$.ajax({
	url: config.baseUrl + LOAD_URL,
	dataType: 'json',
	crossDomain: true,
	success: (response)  => {
		let brands = [];
		let sources = [];
		response.data.brands.forEach(function(val) {
			brands[val.id] = val;
		});

		response.data.sources.forEach(function(val) {
			sources[val.id] = val;
		});

		AppDispatcher.dispatch({
			type: AppConstants.REFERENCES_LOAD_SUCCESS,
			data: {
				brands:  brands,
				sources: sources
			},
		});
	},
	error: function() {
		AppDispatcher.dispatch({
			type: AppConstants.REFERENCES_LOAD_FAIL,
		});
	}
});

export default ReferencesStore;