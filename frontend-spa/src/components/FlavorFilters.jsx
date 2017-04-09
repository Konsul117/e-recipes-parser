import React from "react";
import Checkbox from "react-bootstrap/lib/Checkbox";
import FormControl from "react-bootstrap/lib/FormControl";
import Constants from "../constants/Constants";
import FlavorFiltersModel from "../models/FlavorFiltersModel";
import ReferencesStore from "../stores/ReferencesStore";

const FlavorFilters = React.createClass({
	getInitialState: function() {
		this.filters = new FlavorFiltersModel();
		return {
			brandsList: ReferencesStore.getBrandsList(),
			sourcesList: ReferencesStore.getSourcesList(),
			loadStatus: Constants.LOAD_STATUS_IN_PROCESS
		}
	},

	componentDidMount: function() {
		ReferencesStore.addLoadListener(() => {
			let brandsList = ReferencesStore.getBrandsList();
			let sourcesList = ReferencesStore.getSourcesList();
			if (brandsList.length > 0 && sourcesList.length > 0) {
				this.props.onLoadingReady();
				this.setState({
					brandsList: brandsList,
					sourcesList: sourcesList,
					loadStatus: Constants.LOAD_STATUS_FINISHED
				});
			}
			else {
				this.setState({
					loadStatus: Constants.LOAD_STATUS_ERROR
				});
			}
		});
	},

	/**
	 * Обработчик выбора брендов.
	 *
	 * @param {SyntheticEvent} event Событие
	 */
	handleBrandChange(event) {
		let brandId = parseInt(event.target.getAttribute('value'));
		if (event.target.checked) {
			this.filters.brandsIds.push(brandId);
		}
		else {
			let index = this.filters.brandsIds.indexOf(brandId);

			if (index >= 0) {
				this.filters.brandsIds.splice(index, 1);
			}
		}

		this.props.onFiltersChange(this.filters);
	},

	/**
	 * Обработчик выбора источников.
	 *
	 * @param {SyntheticEvent} event Событие
	 */
	handleSourceChange(event) {
		let sourceId = parseInt(event.target.getAttribute('value'));
		if (event.target.checked) {
			this.filters.sourcesIds.push(sourceId);
		}
		else {
			let index = this.filters.sourcesIds.indexOf(sourceId);

			if (index >= 0) {
				this.filters.sourcesIds.splice(index, 1);
			}
		}

		this.props.onFiltersChange(this.filters);
	},

	/**
	 * Рендер список брендов.
	 *
	 * @return {XML}
	 */
	renderBrandsList() {
		if (this.state.loadStatus !== Constants.LOAD_STATUS_FINISHED) {
			return '';
		}

		return (
			<ul className="list-unstyled">
				{
					this.state.brandsList.map((el) => {
						return <li key={el.id}>
							<Checkbox value={el.id} onChange={this.handleBrandChange}>{el.title}</Checkbox>
						</li>;
					})
				}
			</ul>
		);
	},

	renderSourcesList() {
		if (this.state.loadStatus !== Constants.LOAD_STATUS_FINISHED) {
			return '';
		}

		return (
			<ul className="list-unstyled">
				{
					this.state.sourcesList.map((el) => {
						return <li key={el.id}>
							<Checkbox value={el.id} onChange={this.handleSourceChange}>{el.title}</Checkbox>
						</li>;
					})
				}
			</ul>
		);
	},

	/**
	 * Рендер сообщения для пользователя
	 */
	renderLoadMessage() {
		switch(this.state.loadStatus) {
			case Constants.LOAD_STATUS_IN_PROCESS:
				return (
					<div>
						<span className="glyphicon glyphicon-refresh"></span> Загрузка...
					</div>
				);

			case Constants.LOAD_STATUS_ERROR:
				return (
					<div>
						<span className="glyphicon glyphicon-ban-circle"></span> Ошибка загрузки
					</div>
				);
		}
	},

	/**
	 * Обработчик ввода фильтра по названию.
	 *
	 * @param {SyntheticEvent} event Событие
	 */
	onRecipeNameChange: function(event) {
		this.filters.searchQuery = event.target.value;

		this.props.onFiltersChange(this.filters);
	},

	renderNameBlock: function() {
		return <FormControl onChange={this.onRecipeNameChange} />
	},

	render: function() {
		let data = [
			{value: 'apple', label: 'Apple'},
			{value: 'orange', label: 'Orange'},
			{value: 'banana', label: 'Banana', checked: true} // check by default
		];

		return (
			<div className="flavor-filters">
				<div className="flavor-name">
					{this.renderNameBlock()}
				</div>
				<div className="brands">
					<div className="filters-group-name">Бренды:</div>
					{this.renderBrandsList()}
				</div>
				<div className="sources">
					<div className="filters-group-name">Источники:</div>
					{this.renderSourcesList()}
				</div>
				{this.renderLoadMessage()}
			</div>
		)
	}
});

export default FlavorFilters;