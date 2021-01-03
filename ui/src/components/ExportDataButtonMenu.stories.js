import ExportDataButtonMenu from './ExportDataButtonMenu.vue'

export default {
  title: 'Components/Generic/ExportDataButtonMenu',
  component: ExportDataButtonMenu,
}

const Template = (args, { argTypes }) => ({
  components: { ExportDataButtonMenu },
  props: Object.keys(argTypes),
  template: `
    <v-main>
      <v-container>
        <export-data-button-menu :data="data" :label="label" :file-name="fileName"/>
      </v-container>
    </v-main>
  `,
})

export const TabularData = Template.bind({})
TabularData.args = {
  data: [{ id: 1, value: 'test' }],
}
