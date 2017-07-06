import React from "react";
import FlavorProvider from "../providers/FlavorProvider";
import FlavorRequest from "../models/FlavorRequest";
import Constants from "../constants/Constants";
import ReferencesStore from "../stores/ReferencesStore";

const FlavorList = React.createClass({

	/** @param {FlavorFiltersModel} filters */
	filters: null,
	brandsList: [],

	currentFiltersHash: null,

	getInitialState: function() {
		return {
			loadStatus: Constants.LOAD_STATUS_IN_PROCESS,
			loadedFlavors: new Map(),
		}
	},

	componentWillReceiveProps: function(nextProps) {
		let newFiltersHash = null;
		if (nextProps.filters !== null) {
			newFiltersHash = nextProps.filters.calcHash();
			this.filters = nextProps.filters;
		}

		if (this.currentFiltersHash !== newFiltersHash) {
			this.loadFlavors();
			this.currentFiltersHash = newFiltersHash;
		}
	},

	componentDidMount: function() {
		ReferencesStore.addLoadListener(this.onReferencesLoaded);
	},

	componentWillUnmount() {
		ReferencesStore.removeLoadListener(this.onReferencesLoaded);
	},

	onReferencesLoaded: function() {
		let _brandsList = ReferencesStore.getBrandsList();
		if (_brandsList.size > 0) {
			this.showingBrandsList = _brandsList;
			this.loadFlavors();
		}
		else {
			this.setState({
				loadStatus: Constants.LOAD_STATUS_ERROR,
			});
		}
	},

	/**
	 * Загрузка ароматизаторов.
	 */
	loadFlavors: function() {
		let request = new FlavorRequest();

		if (this.filters !== null) {
			request.nameQuery  = this.filters.searchQuery;
			request.brandsIds  = this.filters.brandsIds;
			request.sourcesIds = this.filters.sourcesIds;
		}

		FlavorProvider.search(request, (promise, data) => {/** @param {RequestPromise} promise */
			let state = {loadStatus: promise.status};

			if (promise.status === Constants.LOAD_STATUS_FINISHED) {/** @param {FlavorsResponse} data */
				state.loadedFlavors = data;

				this.props.onLoadingReady();
			}

			this.setState(state);
		});
	},

	/**
	 *
	 * @param event
	 * @return {boolean}
	 */
	onClickFlavor: function(event) {
		event.preventDefault();

		if (typeof(this.props.onSelectFlavor) === 'function') {
			let id = parseInt(event.target.parentElement.getAttribute('data-id'));
			let flavor = this.state.loadedFlavors.get(id);
			if (flavor !== undefined) {
				this.props.onSelectFlavor(flavor);
			}
		}
	},

	renderFlavorsBlock: function() {
		if (this.state.loadStatus !== Constants.LOAD_STATUS_FINISHED) {
			return;
		}

		if (this.state.loadedFlavors.size === 0) {
			return (
				<div className="alert alert-info">
					Ароматизаторы не найдены
				</div>
			);
		}

		let liArray = [];

		this.state.loadedFlavors.forEach((flavor) => {
			let brandName = null;
			let brand = this.showingBrandsList.get(flavor.brandId);

			if (brand !== undefined) {
				brandName = brand.title;
			}

			liArray.push(<li key={flavor.id}>
				<a href="" onClick={this.onClickFlavor} data-id={flavor.id}>
					<span>{flavor.name}</span>{
					(brandName !== null) ? (
						<span className="brand-name">{brandName}</span>
					) : ''
				}</a>
			</li>);
		});

		return (
			<ul>
				{liArray}
			</ul>
		);
	},

	renderMessageBlock: function() {
		switch(this.state.loadStatus) {
			case Constants.LOAD_STATUS_IN_PROCESS:
				return <span className="glyphicon glyphicon-refresh"> Загрузка...</span>;
			case Constants.LOAD_STATUS_ERROR:
				return <span className="glyphicon glyphicon-ban-circle"> Ошибка загрузки</span>;
		}
	},

	render: function() {
		return (
			<div className='flavor-list'>
				{this.renderFlavorsBlock()}
				{this.renderMessageBlock()}
			</div>
		);
	},
});

export default FlavorList;