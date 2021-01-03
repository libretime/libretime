import Vue from 'vue'
import Vuetify from 'vuetify'
import VueBlobJsonCsv from 'vue-blob-json-csv'
import { makeServer } from './server'

Vue.use(Vuetify)
Vue.use(VueBlobJsonCsv)
Vue.config.productionTip = false

makeServer()
