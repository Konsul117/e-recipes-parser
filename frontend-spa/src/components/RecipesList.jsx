import React from "react";
import ReactComponent from "react/lib/ReactComponent";
import RecipesActions from "../actions/RecipesActions";
import RecipesSearchStore from "../stores/RecipesSearchStore";

/**
 * Компонент поиска и вывода найденных рецептов.
 *
 * В передаваемых свойствах следующие параметры:
 *  flavorsIds          - массив идентификаторов ароматизаторов
 *  flavorsFilterTypeId - идентификатор типа поиска
 */
class RecipesList extends ReactComponent {
	constructor() {
		super();

		this.state = {
			recipes:    null,
			foundCount: null,
			isLoading:  false,
			isError: false,
		};

		this.onLoadRecipes = () => {
			if (RecipesSearchStore.isLoading === true) {
				this.setState({
					isLoading: true,
				});

				return;
			}

			if (RecipesSearchStore.isError === true) {
				this.setState({
					isLoading: false,
					isError:   true,
				});

				return;
			}

			let result = RecipesSearchStore.searchResult;
			this.setState({
				recipes:    result.recipes,
				isLoading:  false,
				isError:    false,
				foundCount: result.totalCount
			})
		}
	}

	componentWillMount() {
		this.performSearch();

		RecipesSearchStore.addLoadListener(this.onLoadRecipes);
	}

	componentWillUnmount() {
		RecipesSearchStore.removeLoadListener(this.onLoadRecipes);
	}

	// shouldComponentUpdate(nextProps, nextState) {
	// 	return (this.state !== nextState);
	// }

	componentDidUpdate(prevProps, prevState) {
		if (prevProps !== this.props) {
			this.performSearch();
		}
	}

	performSearch() {
		let searchModel = {};
		/** @param {RecipesSearchRequest} searchModel */

		if (this.props.flavorsIds !== []) {
			searchModel.flavorsIds = this.props.flavorsIds;
		}

		if (this.props.flavorsFilterTypeId !== null) {
			searchModel.flavorsFilterTypeId = this.props.flavorsFilterTypeId;
		}

		searchModel.limit = 50;

		RecipesActions.findRecipes(searchModel);
	}

	render() {
		if (this.state.recipes === null) {
			return null;
		}

		if (this.state.isLoading === true) {
			return <div className="alert alert-info"><span className="glyphicon glyphicon-refresh"></span> Идёт загрузка</div>;
		}

		if (this.state.isError === true) {
			return <div className="alert alert-danger">Ошибка загрузки</div>;
		}

		return (
			<div>
				{
					(this.state.recipes.length > 0)
						? <div>
							<p>Найдено рецептов: {this.state.foundCount}</p>
						<ul>
							{
								this.state.recipes.map(function(recipe) {/** @param {RecipeItemResponse} recipe */
									return <li key={recipe.id}>
										{recipe.title}
									</li>;
								})
							}
						</ul>
						</div>
						: <div className="alert alert-info">Рецепты не найдены</div>
				}
			</div>
		);
	}
}

export default RecipesList;