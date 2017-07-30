import React from "react";
import Checkbox from "react-bootstrap/lib/Checkbox";
import FormControl from "react-bootstrap/lib/FormControl";
import Constants from "../constants/Constants";
import FlavorFiltersModel from "../models/FlavorFiltersModel";
import ReferencesStore from "../stores/ReferencesStore";
import BrandsList from "./BrandsList.jsx";

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
			let sourcesList = ReferencesStore.getSourcesList();
			if (sourcesList.size > 0) {
				//небольшое допущение - если загрузились источники - то загрузились и бренды и фильтр брендов, условно, готов к выводу
				this.props.onLoadingReady();
				this.setState({
					sourcesList: sourcesList,
					loadStatus:  Constants.LOAD_STATUS_FINISHED
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

	renderSourcesList() {
		if (this.state.loadStatus !== Constants.LOAD_STATUS_FINISHED) {
			return '';
		}

		let buff = [];

		this.state.sourcesList.forEach((el) => {
			buff.push(<li key={el.id}>
				<Checkbox value={el.id} onChange={this.handleSourceChange}>{el.title}</Checkbox>
			</li>);
		});

		return (
			<ul className="list-unstyled">
				{buff}
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

	/**
	 * Обработка смены выбора брендов.
	 *
	 * @param {number[]} brandsIds Массив идентификаторров выбранных брендов
	 */
	onBrandsSelect: function(brandsIds) {
		this.filters.brandsIds = brandsIds;
		this.props.onFiltersChange(this.filters);
	},

	render: function() {
		return (
			<div className="flavor-filters">
				<div className="flavor-name">
					<label>Название ароматизатора</label>
					{this.renderNameBlock()}
				</div>
				<BrandsList onSelectChange={this.onBrandsSelect}/>
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