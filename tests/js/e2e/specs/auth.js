module.exports = {
  before(browser) {
    browser
    .url(browser.launch_url)
  },

  'has hello world content'(browser) {
    browser
    .waitForElementPresent('.top-sidebar', 1000)
    .assert.containsText('body', 'Hello world')
  },

  after(browser) {
    browser.end()
  },

}
