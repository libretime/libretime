import i18n from '@/plugins/i18n';

import AnalyticsPlayoutHistory from './AnalyticsPlayoutHistory.vue';

export default {
    title: 'AnalyticsPlayoutHistory',
    component: AnalyticsPlayoutHistory,
    i18n,
};

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
});

export const NoData = Template.bind({});
NoData.args = {}
