import { createLocalVue, mount } from '@vue/test-utils'

import Vuetify from 'vuetify'

import PlayoutHistoryTemplateSelect from '../../src/components/PlayoutHistoryTemplateSelect.vue'

const localVue = createLocalVue()

describe('PlayoutHistoryTemplateSelect.vue', () => {
  let vuetify

  beforeEach(() => {
    vuetify = new Vuetify()
  })

  const localMount = (options) => {
    return mount(PlayoutHistoryTemplateSelect, {
      localVue,
      vuetify,
      ...options,
    })
  }

  it('should match snapshot', () => {
    const wrapper = localMount()

    expect(wrapper.html()).toMatchInlineSnapshot(`
      <div class="v-input v-input--is-loading v-input--dense theme--light v-text-field v-select">
        <div class="v-input__control">
          <div role="button" aria-haspopup="listbox" aria-expanded="false" aria-owns="list-2" class="v-input__slot">
            <div class="v-select__slot"><label for="input-2" class="v-label theme--light" style="left: 0px; position: absolute;">Template</label>
              <div class="v-select__selections"><input id="input-2" readonly="readonly" type="text" aria-readonly="false" autocomplete="off"></div>
              <div class="v-input__append-inner">
                <div class="v-input__icon v-input__icon--append"><i aria-hidden="true" class="v-icon notranslate mdi mdi-menu-down theme--light"></i></div>
              </div><input type="hidden">
            </div>
            <div class="v-menu">
              <!---->
            </div>
            <div role="progressbar" aria-valuemin="0" aria-valuemax="100" class="v-progress-linear v-progress-linear--absolute theme--light" style="height: 2px;">
              <div class="v-progress-linear__background primary" style="opacity: 0.3; left: 0%; width: 100%;"></div>
              <div class="v-progress-linear__buffer"></div>
              <transition-stub name="fade-transition">
                <div class="v-progress-linear__indeterminate v-progress-linear__indeterminate--active">
                  <div class="v-progress-linear__indeterminate long primary"></div>
                  <div class="v-progress-linear__indeterminate short primary"></div>
                </div>
              </transition-stub>
            </div>
          </div>
          <div class="v-text-field__details">
            <div class="v-messages theme--light">
              <transition-group-stub tag="div" name="message-transition" class="v-messages__wrapper"></transition-group-stub>
            </div>
          </div>
        </div>
      </div>
    `)
  })
})
