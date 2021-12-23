import { DocsContainer } from '@storybook/addon-docs/blocks'
import { themes } from '@storybook/theming'
import React from 'react'

import Vue from 'vue'
import Vuetify from 'vuetify'
import VueI18n from 'vue-i18n'
import vcalendar from '@/plugins/vcalendar';
import { i18nOptions } from '@/plugins/i18n'
import { vuetifyOptions } from '@/plugins/vuetify'

// setup miragejs
import { makeServer } from "@/server"

makeServer()

Vue.config.productionTip = false

// configure Vue to use Vuetify
Vue.use(Vuetify)
Vue.use(VueI18n)
Vue.use(vcalendar)

export const parameters = {
  actions: {
    argTypesRegex: "^on[A-Z].*"
  },
  docs: {
    theme: themes.dark,
    inlineStories: false,
  },
}

// instantiate Vuetify instance with any component/story level params
const vuetify = new Vuetify(vuetifyOptions)
const i18n = new VueI18n(i18nOptions)

// vue/vuetify/i18n/etc decorator
export const decorators = [
  (story, context) => {
    // wrap the passed component within the passed context
    const wrapped = story(context)
    // extend Vue to use Vuetify around the wrapped component
    return Vue.extend({
      vuetify,
      i18n,
      name: "app",
      components: { wrapped },
      props: {
        locale: {
          type: String,
          default: 'en',
        },
      },
      watch: {
        locale: {
          immediate: true,
          handler (val) {
            this.$i18n.locale = val
          }
        }
      },
      template: `
        <div class="libretime-vue">
          <v-app dark>
            <wrapped />
          </v-app>
        </div>
      `
    })
  },
]
