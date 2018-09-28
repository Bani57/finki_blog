const webpack = require('webpack')

module.exports = {
  baseUrl: 'finki_blog',
  configureWebpack: {
    plugins: [
      new webpack.ProvidePlugin({
        jQuery: 'jquery'
      })
    ]
  }
}
