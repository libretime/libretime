// Utilities
import { storyFactory } from '../util/helpers'
import { text, boolean } from '@storybook/addon-knobs'

export default { title: 'BaseCard' }

function genComponent (name) {
  return {
    name,

    render (h) {
      return h('div', this.$slots.default)
    },
  }
}

const story = storyFactory({
  BaseBtn: genComponent('BaseBtn'),
  BaseCard: genComponent('BaseCard'),
})

export const asDefault = () => story({
  props: {
    actions: {
      default: boolean('Actions', false),
    },
    cardText: {
      default: text('Card text', 'Sed augue ipsum, egestas nec, vestibulum et, malesuada adipiscing, dui. Donec sodales sagittis magna. Vestibulum dapibus nunc ac augue. Donec sodales sagittis magna. Duis vel nibh at velit scelerisque suscipit.'),
    },
    divider: {
      default: boolean('Divider', false),
    },
    text: {
      default: boolean('Text', true),
    },
    title: {
      default: boolean('Show title', true),
    },
    titleText: {
      default: text('Title text', 'Card title'),
    },
  },
  template: `
    <base-card>
      <v-card-title v-if="title">{{ titleText }}</v-card-title>

      <v-card-text v-if="text">{{ cardText }}</v-card-text>

      <v-divider v-if="divider"></v-divider>

      <v-card-actions v-if="actions">
        <v-btn text>Cancel</v-btn>

        <v-spacer></v-spacer>

        <base-btn depressed>Accept</base-btn>
      </v-card-actions>
    </base-card>
  `,
})
