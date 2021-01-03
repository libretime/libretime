const prefixer = require('postcss-prefix-selector')

module.exports = {
  // false to make embedding in airtime_mvc easy, can be switched to true once we get rid of the legacy ui
  filenameHashing: false,
  transpileDependencies: ['vuetify'],

  pluginOptions: {
    i18n: {
      locale: 'en',
      fallbackLocale: 'en',
      localeDir: 'locales',
      enableInSFC: true,
    },
  },

  // this chain uses prefixer to wrap all css in a prefix that can be used with <div libretime-vue>,
  // using this we can load the vue css into the legacy fronted without it overriding legacy CSS.
  chainWebpack: (config) => {
    const sassRule = config.module.rule('sass')
    const sassNormalRule = sassRule.oneOfs.get('normal')
    const vuetifyRule = sassRule.oneOf('vuetify').test(/[\\/]vuetify[\\/]src[\\/]/)
    Object.keys(sassNormalRule.uses.entries()).forEach((key) => {
      vuetifyRule.uses.set(key, sassNormalRule.uses.get(key))
    })
    sassRule.oneOfs.delete('normal')
    sassRule.oneOfs.set('normal', sassNormalRule)
    vuetifyRule
      .use('vuetify')
      .loader(require.resolve('postcss-loader'))
      .tap((options = {}) => {
        options.sourceMap = process.env.NODE_ENV !== 'production'
        options.plugins = [
          prefixer({
            prefix: '.libretime-vue',
            transform(prefix, selector, prefixedSelector) {
              let result = prefixedSelector
              if (selector.startsWith('html') || selector.startsWith('body')) {
                result = prefix + selector.substring(4)
              }
              return result
            },
          }),
        ]
        return options
      })
    vuetifyRule.uses.delete('sass-loader')
    vuetifyRule.uses.set('sass-loader', sassNormalRule.uses.get('sass-loader'))
  },
}
