var bootstrap = require('jsonfile').readFileSync(
    __dirname + "/bootstrap.json"
);

module.exports = {
  verbose: true,

  bootstrapCustomizations: "./src/Resources/assets/variables",
  mainSass: "./src/Resources/assets/main",

  styleLoader: require('extract-text-webpack-plugin').extract('style-loader', 'css-loader!sass-loader'),

  scripts: bootstrap.scripts,
  styles: bootstrap.styles
};
