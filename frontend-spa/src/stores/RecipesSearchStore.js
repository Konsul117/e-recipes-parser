import AppEventEmitter from "./AppEventEmitter";
import AppDispatcher from "../dispatchers/AppDispatcher";
import Constants from "../constants/Constants";

class RecipesSearchStore extends AppEventEmitter {

	constructor() {
		super();

		this.searchResult = null;
		this.isError = false;
		this.isLoading = false;

		AppDispatcher.register(action => {
			switch (action.type) {
				case Constants.RECIPES_LOAD_SUCCESS: {
					this.isError = false;
					this.searchResult = action.data;
					this.isLoading = false;

					this.emitLoad();

					break;
				}

				case Constants.RECIPES_LOAD_FAIL: {
					this.isError = true;
					this._brands = [];
					this.isLoading = false;

					this.emitLoad();

					break;
				}

				case Constants.RECIPES_LOADING: {
					this.isError = false;
					this.isLoading = true;

					this.emitLoad();

					break;
				}
			}
		});
	}

}

export default new RecipesSearchStore();