var Encore = require('@symfony/webpack-encore');

Encore
    .setOutputPath('public/build/dev/')
	.setManifestKeyPrefix('public/build/dev/')
	.addEntry('js/app', './assets/js/app.js')
	.addStyleEntry('css/app', './assets/css/app.scss')
    .setPublicPath('/build/dev')
	.enableSassLoader()
	.autoProvidejQuery()
	.disableSingleRuntimeChunk()
;

const dev = Encore.getWebpackConfig();

dev.name = 'dev';

Encore.reset();

Encore
	.setOutputPath('public/build/production/')
	.setManifestKeyPrefix('public/build/production/')
	.addEntry('js/app', './assets/js/app.js')
	.addStyleEntry('css/app', './assets/css/app.scss')
	.setPublicPath('/statb/public/build/production')
	.enableSassLoader()
	.autoProvidejQuery()
	.disableSingleRuntimeChunk()
;

const production = Encore.getWebpackConfig();

dev.name = 'production';

module.exports = [dev, production];
