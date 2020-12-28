import { addDecorator } from '@storybook/vue';

import Vue from 'vue';
import Vuetify from 'vuetify';
import vuetify from '@/plugins/vuetify';
import vcalendar from '@/plugins/vcalendar';
import { makeServer } from "@/server"

Vue.use(Vuetify, vuetify)
Vue.use(vcalendar)

makeServer()

addDecorator(() => ({
  vuetify,
  template: `
    <div libretime-vue>
      <v-app dark>
        <story />
      </v-app>
    </div>
  `,
}));
