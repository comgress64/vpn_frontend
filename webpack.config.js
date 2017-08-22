const buildBaseConfig = require('dq-webpack');
const path = require('path');

const baseConfig = buildBaseConfig(__dirname, {
  babelNodeExcept: ['proxy-polyfill'],
});

const config = baseConfig.merge({

  entry: {
    admin: path.join(__dirname, 'resources', 'assets', 'js', 'admin', 'app'),
    auth: path.join(__dirname, 'resources', 'assets', 'js', 'auth', 'app'),
    superadmin: path.join(__dirname, 'resources', 'assets', 'js', 'superadmin', 'app'),
    user: path.join(__dirname, 'resources', 'assets', 'js', 'user', 'app'),
  },

  resolve: {
    alias: {
      _assets: path.join(__dirname, 'resources', 'assets'),
      _admin: path.join(__dirname, 'resources', 'assets', 'js', 'admin'),
      _auth: path.join(__dirname, 'resources', 'assets', 'js', 'auth'),
      _superadmin: path.join(__dirname, 'resources', 'assets', 'js', 'superadmin'),
      _user: path.join(__dirname, 'resources', 'assets', 'js', 'user'),
      _shared: path.join(__dirname, 'resources', 'assets', 'js', 'shared'),
      _components:   path.join(__dirname, 'resources', 'assets', 'js', 'shared', 'components'),
    }
  },

});

if (process.env.APP_ENV === 'production') {
  config.merge({
  });
}

module.exports = config;
