import Vue from 'vue'
import App from './App.vue'
import vuetify from './plugins/vuetify'
import i18n from './plugins/i18n'
import vcalendar from './plugins/vcalendar'
import vueblobjsoncsv from './plugins/vueblobjsoncsv'
import router from './router'
import { makeServer } from './server'

Vue.config.productionTip = false

if (process.env.NODE_ENV === 'development') {
  makeServer()
}

new Vue({
  vuetify,
  i18n,
  vcalendar,
  vueblobjsoncsv,
  router,
  render: (h) => h(App),
}).$mount('#app')
