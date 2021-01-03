import { createLocalVue, mount } from '@vue/test-utils'

import Vuetify from 'vuetify'

import ExportDataButtonMenu from '../../src/components/ExportDataButtonMenu.vue'

const localVue = createLocalVue()

describe('ExportDataButtonMenu.vue', () => {
  let vuetify

  beforeEach(() => {
    vuetify = new Vuetify()
  })

  const localMount = (options) => {
    return mount(ExportDataButtonMenu, {
      localVue,
      vuetify,
      propsData: {
        data: [],
      },
      ...options,
    })
  }

  it('should match snapshot', () => {
    const wrapper = localMount()

    expect(wrapper.html()).toMatchInlineSnapshot(`
      <div class="v-menu"><button type="button" class="v-btn v-btn--contained v-btn--is-elevated v-btn--has-bg theme--dark v-size--default primary" role="button" aria-haspopup="true" aria-expanded="false"><span class="v-btn__content">Export…</span></button>
        <!---->
      </div>
    `)
  })

  it('should open a menu', () => {
    const wrapper = localMount()

    const button = wrapper.find('button')

    button.trigger('click').then(() => {
      expect(wrapper.html()).toMatchInlineSnapshot(`
        <div class="v-menu"><button type="button" class="v-btn v-btn--contained v-btn--is-elevated v-btn--has-bg theme--dark v-size--default primary" role="button" aria-haspopup="true" aria-expanded="true"><span class="v-btn__content">Export…</span></button>
          <transition-stub name="v-menu-transition">
            <div role="menu" class="v-menu__content theme--light menuable__content__active " style="max-height: auto; min-width: 0px; max-width: auto; top: 12px; left: 0px; transform-origin: top left; z-index: 8; display: none;">
              <div class="v-list v-sheet theme--light">
                <div tabindex="-1" id="list-item-11" class="v-list-item theme--light"><span><div class="v-list-item__title export-data-menu-button-csv">CSV</div>
            
          </span></div>
                <div tabindex="-1" id="list-item-13" class="v-list-item theme--light"><span><div class="v-list-item__title export-data-menu-button-json">JSON</div>
            
          </span></div>
              </div>
            </div>
          </transition-stub>
        </div>
      `)
    })
  })
})
