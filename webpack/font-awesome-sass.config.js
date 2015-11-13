module.exports = {
  styleLoader: require('extract-text-webpack-plugin').extract('style-loader', 'css-loader!sass-loader'),

  styles: {
    "mixins": true,

    "bordered-pulled": true,
    "core": true,
    "fixed-width": true,
    "icons": true,
    "larger": true,
    "list": true,
    "path": true,
    "rotated-flipped": true,
    "animated": true,
    "stacked": true
  }
};
