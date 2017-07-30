import React from "react";
import ReactComponent from "react/lib/ReactComponent";
import ReferencesStore from "../stores/ReferencesStore";
import FormControl from "react-bootstrap/lib/FormControl";
import Constants from "../constants/Constants";
import Checkbox from "react-bootstrap/lib/Checkbox";
import Button from "react-bootstrap/lib/Button";

class BrandsList extends ReactComponent {
	constructor () {
		super();

		this.state = {
			showingBrandsList: ReferencesStore.getBrandsList(),//отфильтрованные бренды по текстовому поиску
			loadStatus:        Constants.LOAD_STATUS_IN_PROCESS,//статус загрузки
			selectedBrandsIds: new Map(),//идентификаторы выбранных брендов
		};
	}

	componentWillMount() {
		this.allBrandsList = new Map();
	}

	componentDidMount() {
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
	}

	/**
	 * Обработка выбора/снятия выбора галочки с бренда.
	 *
	 * @param {SyntheticEvent} event   Событие
	 * @param {number}         brandId Идентификатор бренда
	 */
	handleBrandSelect(event, brandId) {
		let selectedBrandsIds = this.state.selectedBrandsIds;
		if (event.target.checked) {
			if (selectedBrandsIds.get(brandId) === undefined) {
				selectedBrandsIds.set(brandId, true);

				this.setState({
					selectedBrandsIds: selectedBrandsIds
				});
			}
		}
		else {
			if (selectedBrandsIds.get(brandId) === true) {
				selectedBrandsIds.delete(brandId);

				this.setState({
					selectedBrandsIds: selectedBrandsIds
				});
			}
		}

		// this.props.onSelectChange(Array.from(this.selectedBrandsIds.keys()));
	}

	/**
	 * Обработка смены названия фильтрации бренда.
	 *
	 * @param {SyntheticEvent} event Событие
	 */
	onBrandNameChange(event) {
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
	}

	unCheckAllHandler() {
		let selectedBrandsIds = this.state.selectedBrandsIds;
		selectedBrandsIds.clear();
		this.setState({
			showingBrandsList: this.state.showingBrandsList,
			selectedBrandsIds: selectedBrandsIds
		});
		this.props.onSelectChange([]);
	}

	render() {
		if (this.state.loadStatus !== Constants.LOAD_STATUS_FINISHED) {
			return <span className="glyphicon glyphicon-refresh"> Загрузка...</span>;
		}

		let brands = <div></div>;

		if (this.state.showingBrandsList.size > 0) {
			brands =
				<div className="brands-list-container">
					<ul className="list-unstyled brands-list">
						{
							Array.from(this.state.showingBrandsList.values()).map(brand => {
								return (
									<li key={brand.id}>
										<Checkbox value={brand.id} onChange={(e) => this.handleBrandSelect(e, brand.id)} checked={this.state.selectedBrandsIds.get(brand.id) === true}>{brand.title}</Checkbox>
									</li>
								)
							})
						}
					</ul>
					{
						(this.state.selectedBrandsIds.size > 0)
							? <div className="check-controls">
								<Button bsSize="xsmall" bsStyle="default" onClick={() => this.unCheckAllHandler()}>Снять все</Button>
							</div>
							: null
					}

				</div>
		}
		else {
			brands = <div>Бренды не найдены</div>;
		}

		return (
			<div className="brands">
				<div className="filters-group-name">Бренды:</div>
				<FormControl onChange={(e) => this.onBrandNameChange(e)} />
				{brands}
			</div>
		);
	}
}

export default BrandsList;