import Vue from 'vue'
import Vuetify from 'vuetify/lib'

Vue.use(Vuetify)

export const vuetifyOptions = {
  theme: {
    dark: true,
    themes: {
      dark: {
        primary: '#ff5d1a', //formerly #e62129
        secondary: '#0d4b56',
        accent: '#82B1FF',
        error: '#FF5252',
        info: '#2196F3',
        success: '#4CAF50',
        warning: '#FFC107',
        background: '#353535', //formerly #242424
      },
    },
  },
}

export default new Vuetify(vuetifyOptions)
