import React from "react";
import ReactComponent from "react/lib/ReactComponent";
import Radio from "react-bootstrap/lib/Radio";

class RecipeFilters extends ReactComponent {
	constructor() {
		super();

		//поиск рецептов со всеми выбранными ароматизаторами
		this.FLAVORS_FILTER_TYPE_ALL_ID = 1;
		//поиск рецептов с любым из выбранных ароматизаторов
		this.FLAVORS_FILTER_TYPE_ANY_ID = 2;

		this.state = {
			flavors:             new Map(),
			flavorsFilterTypeId: this.FLAVORS_FILTER_TYPE_ALL_ID
		};
	}

	/**
	 * @param {FlavorItemResponse} flavor
	 */
	addNewFlavor(flavor) {
		let flavors = this.state.flavors;
		let isNew = true;

		if (flavors.get(flavor.id) === undefined) {
			flavors.set(flavor.id, flavor);
			this.setState({
				flavors: flavors,
			});
		}
	}

	deleteFlavor(flavorId) {
		let flavors = this.state.flavors;
		if (flavors.delete(flavorId) === true) {
			this.setState({
				flavors: flavors,
			});
		}
	}

	componentDidUpdate(prevProps, prevState) {
		if (prevState !== this.state) {
			this.props.onFilterChanged({
				flavorsIds:          Array.from(this.state.flavors.keys()),
				flavorsFilterTypeId: this.state.flavorsFilterTypeId
			});
		}
	}

	onFilterTypeChange(id) {
		this.setState({
			flavorsFilterTypeId: id
		});
	}

	render() {
		return (
			<div>
				Фильтрация рецептов
				{
					(this.state.flavors.size > 0)
					?
						<div>
							Ароматизаторы:
							<ul>
								{
									Array.from(this.state.flavors.values()).map((flavor) => {
										return <li key={flavor.id}>
											<span>{flavor.name}</span>
											<a href="#" onClick={() => this.deleteFlavor(flavor.id)}>[x]</a>
										</li>
									})
								}
							</ul>

							<div className="form-group">
								<Radio name="filter-type" checked={this.state.flavorsFilterTypeId === this.FLAVORS_FILTER_TYPE_ALL_ID} onChange={() => this.onFilterTypeChange(1)}>Все вместе</Radio>
								<Radio name="filter-type" checked={this.state.flavorsFilterTypeId === this.FLAVORS_FILTER_TYPE_ANY_ID} onChange={() => this.onFilterTypeChange(2)}>Любой</Radio>
							</div>
						</div>
					: null
				}
				{
					//если ни один фильтр не выбран
					(this.state.flavors.size === 0)
						? <div className="alert alert-info">Выберите параметры фильтрации</div>
						: null
				}
			</div>
		);
	}
}

export default RecipeFilters;