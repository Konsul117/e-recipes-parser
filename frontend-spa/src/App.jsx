import React from "react";
import FlavorSearch from "./components/FlavorSearch.jsx";
import RecipesFilters from "./components/RecipesFilters.jsx";
import "./styles/main.scss";

const App = React.createClass({

	/**
	 * @param {FlavorItemResponse} flavor
	 */
	flavorClickHandler: function(flavor) {
		this.refs.recipesFilter.addNewFlavor(flavor);
	},

	render: function() {
		return (
			<div className='App'>
				<div className="col-lg-3">
					<FlavorSearch onFlavorClick={this.flavorClickHandler}/>
				</div>
				<div className="col-lg-3">
					<RecipesFilters ref="recipesFilter"/>
				</div>
			</div>
		);
	},
});

export default App;