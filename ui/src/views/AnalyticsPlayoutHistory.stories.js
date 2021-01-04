import AnalyticsPlayoutHistory from './AnalyticsPlayoutHistory.vue'

export default {
  title: 'Views/Analytics/Playout History',
  component: AnalyticsPlayoutHistory,
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
})

export const DefaultSeed = Template.bind({})
DefaultSeed.args = {}
