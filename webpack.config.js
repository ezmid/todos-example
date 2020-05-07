var Encore = require('@symfony/webpack-encore');

Encore
    .setOutputPath('public/build/')
    .cleanupOutputBeforeBuild()
    .enableSourceMaps(!Encore.isProduction())
    .enableSingleRuntimeChunk()
    .addEntry('js/app', './assets/js/app.js')
    .addStyleEntry('css/app', './assets/css/app.scss')
    .enableSassLoader(function(sassOptions) {}, {
        resolveUrlLoader: false
    })
    .autoProvidejQuery() // @todo Refactor out
    .enableVersioning()
    .setPublicPath('/build')
    .setManifestKeyPrefix('build')
;

module.exports = Encore.getWebpackConfig();
