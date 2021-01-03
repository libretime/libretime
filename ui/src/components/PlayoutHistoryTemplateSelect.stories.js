import PlayoutHistoryTemplateSelect from './PlayoutHistoryTemplateSelect'

export default {
  title: 'Components/Analytics/Playout History/TemplateSelect',
  component: PlayoutHistoryTemplateSelect,
  parameters: {
    actions: {
      handles: ['change'],
    },
  },
  argTypes: {
    change: {
      description: 'Fires when a template is selected',
      action: 'change',
    },
  },
}

const Template = (args, { argTypes }) => ({
  components: { PlayoutHistoryTemplateSelect },
  props: Object.keys(argTypes),
  template: `
    <v-main>
      <v-container>
        <playout-history-template-select/>
      </v-container>
    </v-main>
  `,
})

export const DefaultSeed = Template.bind({})
DefaultSeed.args = {}
