import AppEventEmitter from "./AppEventEmitter";
import AppDispatcher from "../dispatchers/AppDispatcher";
import Constants from "../constants/Constants";

class ReferencesStore extends AppEventEmitter {

	constructor() {
		super();

		this._brands = [];
		this._sources = [];
		this._isDataLoaded = false;

		AppDispatcher.register(action => {
			switch(action.type) {
				case Constants.REFERENCES_LOAD_SUCCESS: {
					this._isDataLoaded = true;
					this._brands = action.data.brands;
					this._sources = action.data.sources;

					this.emitLoad();

					break;
				}

				case Constants.REFERENCES_LOAD_FAIL: {
					this._isDataLoaded = false;
					this._brands = [];

					this.emitLoad();

					break;
				}
			}
		});
	}

	getBrandsList() {
		return this._brands;
	}

	getSourcesList() {
		return this._sources;
	}

	addLoadListener(callback) {
		super.addLoadListener(callback);

		//если данные уже загружены, то сразу вызываем коллбэк
		if (this._isDataLoaded === true) {
			callback();
		}
	}
}

export default new ReferencesStore();