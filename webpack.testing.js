const buildBaseConfig = require('dq-webpack/webpack.common.testing');
const path = require('path');

const baseConfig = buildBaseConfig(__dirname, {});

const config = baseConfig.merge({

  resolve: {
    alias: {
      _assets: path.join(__dirname, 'resources', 'assets'),
      _admin: path.join(__dirname, 'resources', 'app', 'vue', 'admin'),
      _shared: path.join(__dirname, 'resources', 'app', 'vue', 'shared'),
      _components:   path.join(__dirname, 'resources', 'app', 'vue', 'shared', 'components'),
      config:   path.join(__dirname, 'config'),
      spec_helper: path.join(__dirname, 'tests', 'js', 'unit', 'spec_helper'),
      _semantic: path.join(__dirname, 'node_modules', 'semantic-ui-css', 'semantic.js'),
    }
  },

});

module.exports = config;
