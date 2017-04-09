import React from "react";
import FlavorList from "./FlavorList.jsx";
import FlavorFilters from "./FlavorFilters.jsx";

const FlavorSearch = React.createClass({

	isFlavorsReady: false,
	isFiltersReady: false,

	getInitialState: function() {
		return {
			filters: null,
			allComponentsReady: false,
		}
	},

	filtersChangeHandler: function(filters) {
		this.setState({
			filters: filters
		});
	},

	selectRecipeHandler: function(id) {
		console.log('Выбран ароматизатор ' + id);
	},

	flavorsReadyHandler: function() {
		this.isFlavorsReady = true;

		this.updateReadyState();
	},

	filtersReadyHandler: function() {
		this.isFiltersReady = true;

		this.updateReadyState();
	},

	updateReadyState: function() {
		if (this.isFlavorsReady && this.isFiltersReady) {
			this.setState({
				allComponentsReady: true,
			});
		}
	},

	render: function() {
		let wrapperClass = '';
		let messageBlock = '';

		if (this.state.allComponentsReady === false) {
			wrapperClass += ' hidden';
			messageBlock = (
				<div className="glyphicon glyphicon-refresh"> Загрузка...</div>
			);
		}

		return (
			<div className="flavor-search">
				<div className={wrapperClass}>
					<FlavorFilters onFiltersChange={this.filtersChangeHandler} onLoadingReady={this.filtersReadyHandler}/>
					<FlavorList filters={this.state.filters} onSelectRecipe={this.selectRecipeHandler} onLoadingReady={this.flavorsReadyHandler}/>
				</div>
				{messageBlock}
			</div>
		);
	}
});

export default FlavorSearch;