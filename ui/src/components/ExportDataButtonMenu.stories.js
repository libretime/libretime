import { action } from '@storybook/addon-actions';
import { i18n } from '../plugins/i18n';

import ExportDataButtonMenu from '../../src/components/ExportDataButtonMenu.vue';

export default {
    title: 'ExportDataButtonMenu',
    component: ExportDataButtonMenu,
    i18n,
};

const Template = (args, { argTypes }) => ({
  components: { ExportDataButtonMenu },
  props: Object.keys(argTypes),
  template: '<ExportDataButtonMenu/>',
  i18n,
});

export const NoData = Template.bind({});
NoData.args = {}

export const TabularData = Template.bind({});
TabularData.args = {
  data: '[{"id": 1, "value": "test"}]'
}
