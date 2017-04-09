let webpack = require('webpack');

let path = require('path');

module.exports = {
	entry:  "./src/main.js",
	output: {
		path: __dirname + '/public/build/',
		publicPath: "build/",
		filename:   "bundle.js"
	},
	module: {
		loaders: [
			{
				test:    /\.js$/,
				loader:  "babel",
				exclude: [/node_modules/, /public/]
			},
			{
				test:    /\.css$/,
				loader:  "style-loader!css-loader!resolve-url-loader",
				exclude: [/node_modules/, /public/]
			},
			{
				test:    /\.scss$/,
				loader:  "style-loader!css-loader!resolve-url-loader!sass-loader?sourceMap",
				exclude: [/node_modules/, /public/]
			},
			{
				test:    /\.less$/,
				loader:  "style-loader!css-loader!autoprefixer-loader!less",
				exclude: [/node_modules/, /public/]
			},
			{
				test:   /\.gif$/,
				loader: "url-loader?limit=10000&mimetype=image/gif"
			},
			{
				test:   /\.jpg$/,
				loader: "url-loader?limit=10000&mimetype=image/jpg"
			},
			{
				test:   /\.png$/,
				loader: "url-loader?limit=10000&mimetype=image/png"
			},
			{
				test:   /\.svg/,
				loader: "url-loader?limit=26000&mimetype=image/svg+xml"
			},
			{
				test:    /\.jsx$/,
				loader:  "react-hot!babel",
				exclude: [/node_modules/, /public/]
			},
			{
				test:   /\.json$/,
				loader: "json-loader"
			},
			// { test: /.woff2?(\?v=\d+.\d+.\d+)?$/, loader: "url?limit=10000&minetype=application/font-woff" },
			{test: /\.(png|jpg|svg|woff|woff2)?(\?v=\d+.\d+.\d+)?$/, loader: 'url-loader?limit=8192'},
			{test: /\.(eot|ttf)$/, loader: 'file-loader'}
		],
		exports: {
			plugins: [
				new webpack.NewWatchingPlugin()
			]
		}
	}
};