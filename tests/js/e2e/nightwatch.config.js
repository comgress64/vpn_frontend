module.exports = {
  'src_folders': ['tests/js/e2e/specs'],
  'output_folder': 'tests/js/e2e/reports',
  'custom_commands_path': [
    'node_modules/nightwatch-helpers/commands',
    'node_modules/nightwatch-custom-commands-assertions/js/commands',
    'tests/js/e2e/commands',
  ],
  'custom_assertions_path': ['node_modules/nightwatch-helpers/assertions', 'tests/js/e2e/assertions'],

  'selenium': {
    'start_process': true,
    'server_path': 'node_modules/selenium-server/lib/runner/selenium-server-standalone-3.0.1.jar',
    'host': '127.0.0.1',
    'port': 4444,
    'cli_args': {
      'webdriver.chrome.driver': 'node_modules/chromedriver/lib/chromedriver/chromedriver'
    }
  },

  'test_settings': {
    'default': {
      'launch_url': 'http://test@test.com:test@' + process.env.APP_TESTING_HOST + ':8000',
      'selenium_port': 4444,
      'selenium_host': 'localhost',
      'silent': true,
      // 'end_session_on_fail': false,
      'screenshots': {
        'enabled': true,
        'on_failure': true,
        'on_error': false,
        'path': 'tests/js/e2e/screenshots'
      }
    },

    'chrome': {
      'desiredCapabilities': {
        'browserName': 'chrome',
        'javascriptEnabled': true,
        'acceptSslCerts': true
      }
    },
  }
}
