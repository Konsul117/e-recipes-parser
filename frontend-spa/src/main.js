import ReactDOM from 'react-dom';
import React from 'react';
import {Router, Route, Redirect, hashHistory} from 'react-router';
import App from './App.js';

ReactDOM.render(
	<Router history={hashHistory}>
		<Route path='/' component={App}>

		</Route>
	</Router>,
	document.getElementById('mount-point')
);