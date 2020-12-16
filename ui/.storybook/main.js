const path = require('path');

module.exports = {
  stories: ['../src/**/*.stories.@(js|mdx)'],
  addons: ['@storybook/addon-essentials'],
  webpackFinal: async (config, { configType }) => {
    // Configure sass for Vuetify
    config.module.rules.push({
      test: /\.sass$/,
      use: ['style-loader', 'css-loader', 'sass-loader'],
      include: path.resolve(__dirname, '../'),
    });
    // register Vue
    config.module.rules.push({
      resolve: {
        alias: {
          '@': path.resolve(__dirname, '../src'),
          vue: 'vue/dist/vue.js',
          'vue$': 'vue/dist/vue.esm.js',
        },
      },
    });
    return config;
  },
}
