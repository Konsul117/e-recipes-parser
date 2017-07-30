import React from "react";
import ReactComponent from "react/lib/ReactComponent";
import RecipesFilters from "./RecipesFilters.jsx";
import RecipesList from "./RecipesList.jsx";

class RecipesBlock extends ReactComponent {
	constructor() {
		super();

		this.state = {
			recipesFilters: {
				flavorsIds:          [],
				flavorsFilterTypeId: null
			}
		};
	}

	addNewFlavor(flavor) {
		this.refs.recipesFilter.addNewFlavor(flavor);
	}

	onFilterChanged(filters) {
		this.setState({
			recipesFilters: filters,
		});
	}

	render() {
		return <div>
			<RecipesFilters ref="recipesFilter" onFilterChanged={(filters) => this.onFilterChanged(filters)}/>
			<RecipesList flavorsIds={this.state.recipesFilters.flavorsIds} flavorsFilterTypeId={this.state.recipesFilters.flavorsFilterTypeId}/>
		</div>;
	}
}

export default RecipesBlock;