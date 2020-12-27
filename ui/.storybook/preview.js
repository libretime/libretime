import { addDecorator } from '@storybook/vue';

import Vue from 'vue';
import Vuetify from 'vuetify';
import vuetify from '@/plugins/vuetify';
import vcalendar from '@/plugins/vcalendar';

Vue.use(Vuetify, vuetify)
Vue.use(vcalendar)

addDecorator(() => ({
  vuetify,
  template: `
    <v-app dark>
      <story />
    </v-app>
  `,
}));
