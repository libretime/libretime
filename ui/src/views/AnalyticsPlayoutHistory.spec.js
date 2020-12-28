import { createLocalVue, mount } from '@vue/test-utils'

import Vuetify from 'vuetify'
import vcalendar from '@/plugins/vcalendar';

import AnalyticsPlayoutHistory from '../../src/views/AnalyticsPlayoutHistory.vue';

describe('AnalyticsPlayoutHistory.vue', () => {
  const localVue = createLocalVue()
  let vuetify

  beforeEach(() => {
    localVue.use(vcalendar)

    vuetify = new Vuetify()
  })

  it('should match snapshot', () => {
    const wrapper = mount(AnalyticsPlayoutHistory, {
      localVue,
      vuetify,
      mocks: {
        // Mock VueI18n
        $t: () => {}
      },
    })

    expect(wrapper.html()).toMatchSnapshot()
  })
})
