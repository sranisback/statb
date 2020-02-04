var Encore = require('@symfony/webpack-encore');
const Dotenv = require('dotenv');

Encore
    .setOutputPath('public/build/dev/')
	.setManifestKeyPrefix('public/build/dev/')
	.addEntry('js/app', './assets/js/app.js')
	.addStyleEntry('css/app', './assets/css/app.scss')
    .setPublicPath('/build/dev')
	.enableSassLoader()
	.autoProvidejQuery()
	.disableSingleRuntimeChunk()
	.configureDefinePlugin(options => {
		const env = Dotenv.config();

		if (env.error) {
			throw env.error;
		}

		options['process.env'].ENV = JSON.stringify(env.parsed.APP_ENV);
	})
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
	.configureDefinePlugin(options => {
		const env = Dotenv.config();

		if (env.error) {
			throw env.error;
		}

		options['process.env'].ENV = JSON.stringify(env.parsed.APP_ENV);
	})
;

const production = Encore.getWebpackConfig();

production.name = 'production';

module.exports = [dev, production];
