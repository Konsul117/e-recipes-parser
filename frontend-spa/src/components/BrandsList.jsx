import React from "react";
import ReferencesStore from "../stores/ReferencesStore";
import FormControl from "react-bootstrap/lib/FormControl";
import Constants from "../constants/Constants";
import Checkbox from "react-bootstrap/lib/Checkbox";
import Button from "react-bootstrap/lib/Button";

const BrandsList = React.createClass({
	getInitialState: function() {
		this.selectedBrandsIds = [];
		this.allBrandsList = new Map();
		return {
			showingBrandsList: ReferencesStore.getBrandsList(),
			loadStatus:        Constants.LOAD_STATUS_IN_PROCESS
		}
	},

	componentDidMount: function() {
		ReferencesStore.addLoadListener(() => {
			let brandsList = ReferencesStore.getBrandsList();

			if (brandsList.size > 0) {
				this.allBrandsList = brandsList;
				this.setState({
					showingBrandsList: brandsList,
					loadStatus:        Constants.LOAD_STATUS_FINISHED
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
	 * Обработка выбора/снятия выбора галочки с бренда.
	 *
	 * @param {SyntheticEvent} event Событие
	 */
	handleBrandSelect: function(event) {
		let brandId = parseInt(event.target.getAttribute('value'));
		if (event.target.checked) {
			this.selectedBrandsIds.push(brandId);
		}
		else {
			let index = this.selectedBrandsIds.indexOf(brandId);

			if (index >= 0) {
				this.selectedBrandsIds.splice(index, 1);
			}
		}

		this.props.onSelectChange(this.selectedBrandsIds);
	},

	/**
	 * Обработка смены названия фильтрации бренда.
	 *
	 * @param {SyntheticEvent} event Событие
	 */
	onBrandNameChange: function(event) {
		//фильтруем бренды
		let query = event.target.value;
		let resultBrands = new Map();
		this.allBrandsList.forEach(function(brand, id) {
			if (brand.title.toLowerCase().indexOf(query.toLowerCase()) !== -1) {
				resultBrands.set(id, brand);
			}
		});

		this.setState({
			showingBrandsList: resultBrands,
		});
	},

	checkBrandIsSelected(brandId) {
		let result = false;

		for(let id in this.selectedBrandsIds) {
			result = (this.selectedBrandsIds[id] === brandId);

			if (result === true) {
				break;
			}
		}

		return result;
	},

	unCheckAllHandler: function() {
		this.selectedBrandsIds = [];
		this.setState({
			showingBrandsList: this.state.showingBrandsList
		});
		this.props.onSelectChange(this.selectedBrandsIds);
	},

	render: function() {
		if (this.state.loadStatus !== Constants.LOAD_STATUS_FINISHED) {
			return <span className="glyphicon glyphicon-refresh"> Загрузка...</span>;
		}

		let brands = <div></div>;

		if (this.state.showingBrandsList.size > 0) {
			let buff = [];

			this.state.showingBrandsList.forEach((el) => {
				let isChecked = this.checkBrandIsSelected(el.id);

				buff.push(<li key={el.id}>
					<Checkbox value={el.id} onChange={this.handleBrandSelect} checked={isChecked}>{el.title}</Checkbox>
				</li>);
			});

			brands =
				<div className="brands-list-container">
					<ul className="list-unstyled brands-list">
						{buff}
					</ul>
					<div className="check-controls">
						<Button bsSize="xsmall" bsStyle="default" onClick={this.unCheckAllHandler}>Снять все</Button>
					</div>
				</div>
		}
		else {
			brands = <div>Бренды не найдены</div>;
		}

		return (
			<div className="brands">
				<div className="filters-group-name">Бренды:</div>
				<FormControl onChange={this.onBrandNameChange} />
				{brands}
			</div>
		);
	}
});

export default BrandsList;