import Vue from 'vue'
import App from './App.vue'
import vuetify from './plugins/vuetify';
import i18n from './plugins/i18n'
import vcalendar from './plugins/vcalendar';
import router from './router'

Vue.config.productionTip = false

new Vue({
  vuetify,
  i18n,
  vcalendar,
  router,
  render: h => h(App)
}).$mount('#app')
