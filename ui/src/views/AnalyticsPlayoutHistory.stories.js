import i18n from '@/plugins/i18n'

import AnalyticsPlayoutHistory from './AnalyticsPlayoutHistory.vue'

export default {
  title: 'Views/Analytics/Playout History',
  component: AnalyticsPlayoutHistory,
  i18n,
}

const Template = (args, { argTypes }) => ({
  components: { AnalyticsPlayoutHistory },
  props: Object.keys(argTypes),
  template: `
    <v-main>
      <v-container>
        <AnalyticsPlayoutHistory/>
      </v-container>
    </v-main>
  `,
  i18n,
})

export const DefaultSeed = Template.bind({})
DefaultSeed.args = {}
