import { addDecorator } from '@storybook/vue';
import vuetify from './vuetify_storybook';

addDecorator(() => ({
  vuetify,
  template: `
    <v-app>
      <story />
    </v-app>
    `,
}));
