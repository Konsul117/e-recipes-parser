import React from "react";
import FlavorSearch from "./components/FlavorSearch.jsx";
import "./styles/main.scss";

const App = React.createClass({

	render: function() {
		return (
			<div className='App'>
				<div className="col-lg-3">
					<FlavorSearch/>
				</div>
			</div>
		);
	},
});

export default App;