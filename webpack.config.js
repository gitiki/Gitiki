"use strict";

var ExtractTextPlugin = require("extract-text-webpack-plugin");

module.exports = {
  entry: [
    "font-awesome-sass!./webpack/font-awesome-sass.config.js",
    "bootstrap-sass!./webpack/bootstrap-sass.config.js"
  ],
  output: {
    filename: "gitiki.js"
  },
  module: {
    loaders: [
      // the url-loader uses DataUrls.
      // the file-loader emits files.
      { test: /\.woff(2)?(\?v=[0-9]\.[0-9]\.[0-9])?$/, loader: "url-loader?limit=10000&mimetype=application/font-woff" },
      { test: /\.(ttf|eot|svg)(\?v=[0-9]\.[0-9]\.[0-9])?$/, loader: "file-loader" }
    ]
  },
  plugins: [
    new ExtractTextPlugin("gitiki.css")
  ]
}
