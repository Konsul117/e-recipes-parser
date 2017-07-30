import React from "react";
import ReactComponent from "react/lib/ReactComponent";
import FlavorSearch from "./components/FlavorSearch.jsx";
import RecipesBlock from "./components/RecipesBlock.jsx";
import ReferencesActions from "./actions/ReferencesActions";
import "./styles/main.scss";

class App extends ReactComponent {

	componentWillMount() {
		ReferencesActions.loadReferences();
	}

	/**
	 * @param {FlavorItemResponse} flavor
	 */
	flavorClickHandler(flavor) {
		this.refs.recipesBlock.addNewFlavor(flavor);
	}

	render() {
		return (
			<div className='App'>
				<div className="col-lg-3">
					<FlavorSearch onFlavorClick={(flavor) => this.flavorClickHandler(flavor)}/>
				</div>
				<div className="col-lg-3">
					<RecipesBlock ref="recipesBlock"/>
				</div>
			</div>
		);
	}
}

export default App;