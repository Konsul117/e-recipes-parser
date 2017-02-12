import React from 'react';
import { Link } from 'react-router';
import Button from 'react-bootstrap/lib/Button';


import './styles/main.scss';
// import './styles/main.less';

const App = React.createClass({

	btnClick: function(event) {
		console.log(event.target.getAttribute('data-id'));
	},

	render: function() {
		return (
			<div className='App'>
				<div className="col-lg-2">
					<Button bsStyle="primary" onClick={this.btnClick} data-id="1">Кнопка 1</Button>
				</div>
				<div className="col-lg-2">
					<Button bsStyle="default" onClick={this.btnClick} data-id="2">Кнопка 2</Button>
				</div>
				<div className="qwe">qqq</div>
				{this.props.children}
			</div>
		);
	},
});

export default App;