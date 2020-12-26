import { createLocalVue, mount } from '@vue/test-utils'

import Vuetify from 'vuetify'

import ExportDataButtonMenu from '../../src/components/ExportDataButtonMenu.vue';

describe('ExportDataButtonMenu.vue', () => {
  const localVue = createLocalVue()
  let vuetify

  beforeEach(() => {
    vuetify = new Vuetify()
  })

  it('should match snapshot', () => {
    const wrapper = mount(ExportDataButtonMenu, {
      localVue,
      vuetify,
      mocks: {
        // Mock VueI18n
        $t: () => {}
      }
    })

    expect(wrapper.html()).toMatchSnapshot()
  })
})
