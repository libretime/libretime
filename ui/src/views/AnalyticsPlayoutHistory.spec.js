import { createLocalVue, mount } from '@vue/test-utils'

import Vuetify from 'vuetify'
import vcalendar from '@/plugins/vcalendar'

import AnalyticsPlayoutHistory from '../../src/views/AnalyticsPlayoutHistory.vue'

const localVue = createLocalVue()

describe('AnalyticsPlayoutHistory.vue', () => {
  let vuetify

  beforeEach(() => {
    vuetify = new Vuetify()
    localVue.use(vcalendar)
  })

  const localMount = (options) => {
    return mount(AnalyticsPlayoutHistory, {
      localVue,
      vuetify,
      mocks: {
        // Mock VueI18n
        $t: (text) => text,
      },
      ...options,
    })
  }

  it('should match snapshot', () => {
    const wrapper = localMount()

    expect(wrapper.html()).toMatchInlineSnapshot(`
      <div class="v-sheet theme--light"><span><div class="row"><div class="col-md-2 col-12"><h2>Playout History</h2></div> <div class="spacer"></div> <div class="col-md-2 col-12"><div class="v-input v-input--is-loading v-input--dense theme--light v-text-field v-select"><div class="v-input__control"><div role="button" aria-haspopup="listbox" aria-expanded="false" aria-owns="list-9" class="v-input__slot"><div class="v-select__slot"><label for="input-9" class="v-label theme--light" style="left: 0px; position: absolute;">Template</label><div class="v-select__selections"><input id="input-9" readonly="readonly" type="text" aria-readonly="false" autocomplete="off"></div><div class="v-input__append-inner"><div class="v-input__icon v-input__icon--append"><i aria-hidden="true" class="v-icon notranslate mdi mdi-menu-down theme--light"></i></div></div><input type="hidden"></div><div class="v-menu"><!----></div><div role="progressbar" aria-valuemin="0" aria-valuemax="100" class="v-progress-linear v-progress-linear--absolute theme--light" style="height: 2px;"><div class="v-progress-linear__background primary" style="opacity: 0.3; left: 0%; width: 100%;"></div><div class="v-progress-linear__buffer"></div><transition-stub name="fade-transition"><div class="v-progress-linear__indeterminate v-progress-linear__indeterminate--active"><div class="v-progress-linear__indeterminate long primary"></div><div class="v-progress-linear__indeterminate short primary"></div></div></transition-stub></div></div><div class="v-text-field__details"><div class="v-messages theme--light"><transition-group-stub tag="div" name="message-transition" class="v-messages__wrapper"></transition-group-stub></div></div></div></div></div> <div class="col-md-2 col-12"><div class="v-input v-input--is-readonly v-input--dense theme--light v-text-field"><div class="v-input__control"><div class="v-input__slot"><div class="v-text-field__slot"><label for="input-16" class="v-label theme--light" style="left: 0px; position: absolute;">Start Time</label><input id="input-16" readonly="readonly" type="text"></div></div><div class="v-text-field__details"><div class="v-messages theme--light"><transition-group-stub tag="div" name="message-transition" class="v-messages__wrapper"></transition-group-stub></div></div></div></div></div> <div class="col-md-2 col-12"><div class="v-input v-input--is-readonly v-input--dense theme--light v-text-field"><div class="v-input__control"><div class="v-input__slot"><div class="v-text-field__slot"><label for="input-19" class="v-label theme--light" style="left: 0px; position: absolute;">End Time</label><input id="input-19" readonly="readonly" type="text"></div></div><div class="v-text-field__details"><div class="v-messages theme--light"><transition-group-stub tag="div" name="message-transition" class="v-messages__wrapper"></transition-group-stub></div></div></div></div></div> <div class="col-md-2 col-12"><div class="v-menu"><button type="button" class="v-btn v-btn--contained v-btn--is-elevated v-btn--has-bg theme--dark v-size--default primary" role="button" aria-haspopup="true" aria-expanded="false"><span class="v-btn__content">Export…</span></button>
        <!---->
      </div>
      </div>
      </div>
      <div data-v-4cb7b681="" class="vc-popover-content-wrapper">
        <transition-stub data-v-4cb7b681="" name="slide-fade" appear="true"></transition-stub>
      </div></span>
      <div class="v-data-table v-data-table--dense v-data-table--has-bottom theme--light">
        <div class="v-data-table__wrapper">
          <table>
            <colgroup></colgroup>
            <thead class="v-data-table-header">
              <tr></tr>
            </thead>
            <thead>
              <tr class="v-data-table__progress">
                <th colspan="0" class="column">
                  <div role="progressbar" aria-valuemin="0" aria-valuemax="100" class="v-progress-linear v-progress-linear--absolute theme--light" style="height: 4px;">
                    <div class="v-progress-linear__background primary" style="opacity: 0.3; left: 0%; width: 100%;"></div>
                    <div class="v-progress-linear__buffer"></div>
                    <transition-stub name="fade-transition">
                      <div class="v-progress-linear__indeterminate v-progress-linear__indeterminate--active">
                        <div class="v-progress-linear__indeterminate long primary"></div>
                        <div class="v-progress-linear__indeterminate short primary"></div>
                      </div>
                    </transition-stub>
                  </div>
                </th>
              </tr>
            </thead>
            <tbody>
              <tr class="v-data-table__empty-wrapper">
                <td colspan="0">Loading items...</td>
              </tr>
            </tbody>
          </table>
        </div>
        <div class="v-data-footer">
          <div class="v-data-footer__select">Rows per page:<div class="v-input v-input--hide-details v-input--is-label-active v-input--is-dirty theme--light v-text-field v-select">
              <div class="v-input__control">
                <div role="button" aria-haspopup="listbox" aria-expanded="false" aria-owns="list-34" class="v-input__slot">
                  <div class="v-select__slot">
                    <div class="v-select__selections">
                      <div class="v-select__selection v-select__selection--comma">10</div><input aria-label="Rows per page:" id="input-34" readonly="readonly" type="text" aria-readonly="false" autocomplete="off">
                    </div>
                    <div class="v-input__append-inner">
                      <div class="v-input__icon v-input__icon--append"><i aria-hidden="true" class="v-icon notranslate mdi mdi-menu-down theme--light"></i></div>
                    </div><input type="hidden" value="10">
                  </div>
                  <div class="v-menu">
                    <!---->
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="v-data-footer__pagination">–</div>
          <div class="v-data-footer__icons-before"><button type="button" disabled="disabled" class="v-btn v-btn--disabled v-btn--flat v-btn--icon v-btn--round v-btn--text theme--light v-size--default" aria-label="Previous page"><span class="v-btn__content"><i aria-hidden="true" class="v-icon notranslate mdi mdi-chevron-left theme--light"></i></span></button></div>
          <div class="v-data-footer__icons-after"><button type="button" disabled="disabled" class="v-btn v-btn--disabled v-btn--flat v-btn--icon v-btn--round v-btn--text theme--light v-size--default" aria-label="Next page"><span class="v-btn__content"><i aria-hidden="true" class="v-icon notranslate mdi mdi-chevron-right theme--light"></i></span></button></div>
        </div>
      </div>
      </div>
    `)
  })
})
