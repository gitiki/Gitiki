"use strict";

var ExtractTextPlugin = require("extract-text-webpack-plugin");
var webpack = require("webpack");
var glob = require("glob");

var entries = {
  "font-awesome": "font-awesome-sass!./webpack/font-awesome-sass.config.js",
  "bootstrap": "bootstrap-sass!./webpack/bootstrap-sass.config.js"
};
for (let entry of glob.sync("./webpack/*.entry.js")) {
  entries[entry.match(/webpack\/(.+).entry.js/)[1]] = entry;
}

module.exports = {
  entry: entries,
  output: {
    filename: "[name].js"
  },
  module: {
    loaders: [
      // the url-loader uses DataUrls.
      // the file-loader emits files.
      { test: /\.woff(2)?(\?v=[0-9]\.[0-9]\.[0-9])?$/, loader: "url-loader?limit=10000&mimetype=application/font-woff" },
      { test: /\.(ttf|eot|svg)(\?v=[0-9]\.[0-9]\.[0-9])?$/, loader: "file-loader" },
      { test: /\.css$/, loader: ExtractTextPlugin.extract("style-loader", "css-loader") }
    ]
  },
  plugins: [
    new ExtractTextPlugin("[name].css"),
    new webpack.ProvidePlugin({
      $: "jquery",
      jQuery: "jquery"
    })
  ]
}
