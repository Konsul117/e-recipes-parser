import React from "react";
import Constants from "../constants/Constants";
import ReferencesStore from "../stores/ReferencesStore";

const FlavorFilters = React.createClass({
	getInitialState: function() {
		return {
			flavors: new Map()
		};
	},

	/**
	 * @param {FlavorItemResponse} flavor
	 */
	addNewFlavor: function(flavor) {
		let flavors = this.state.flavors;
		let isNew = true;

		if (flavors.get(flavor.id) === undefined) {
			flavors.set(flavor.id, flavor);
			this.setState({
				flavors: flavors,
			});
		}
	},

	deleteFlavor: function(event) {
		let id = parseInt(event.target.parentElement.getAttribute('data-id'));
		let flavors = this.state.flavors;

		if (flavors.delete(id) === true) {
			this.setState({
				flavors: flavors,
			});
		}
	},

	render: function() {
		if (this.state.flavors.size > 0) {
			let buff = [];
			this.state.flavors.forEach((flavor) => {
				buff.push(
					<li key={flavor.id} data-id={flavor.id}>
						<span>{flavor.name}</span>
						<a href="#" onClick={this.deleteFlavor}>[x]</a>
					</li>
				);
			});
			return <ul>
				{buff}
			</ul>;
		}
		else {
			return <div>Пока ничего нет</div>;
		}
	}
});

export default FlavorFilters;