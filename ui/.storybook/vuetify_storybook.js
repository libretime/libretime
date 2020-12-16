import Vue from 'vue';
import Vuetify from 'vuetify'; // loads all components
import 'vuetify/dist/vuetify.min.css'; // all the css for components
import config from '@/plugins/vuetify'; // basic config with theme

Vue.use(Vuetify);

export default new Vuetify(config);
