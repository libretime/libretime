module.exports = {
  // false to make embedding in airtime_mvc easy, can be switched to true once we get rid of the legacy ui
  filenameHashing: false,
  "transpileDependencies": [
    "vuetify"
  ],

  pluginOptions: {
    i18n: {
      locale: 'en',
      fallbackLocale: 'en',
      localeDir: 'locales',
      enableInSFC: true
    }
  }
}
