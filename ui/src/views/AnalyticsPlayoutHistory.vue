<template>
  <v-sheet>
    <vc-date-picker v-model="range" is-dark is-range mode="dateTime">
      <template #default="{ inputValue, inputEvents }">
        <v-row>
          <v-col cols="12" md="2">
            <h2>{{ $t('Playout History') }}</h2>
          </v-col>
          <v-spacer />
          <v-col cols="12" md="2">
            <playout-history-template-select :label="$t('Template')" @change="chooseTemplate" />
          </v-col>
          <v-col cols="12" md="2">
            <v-text-field
              dense
              readonly
              :disabled="!selected"
              :label="$t('Start Time')"
              :value="inputValue.start"
              v-on="inputEvents.start"
            />
          </v-col>
          <v-col cols="12" md="2">
            <v-text-field
              dense
              readonly
              :disabled="!selected"
              :label="$t('End Time')"
              :value="inputValue.end"
              v-on="inputEvents.end"
            />
          </v-col>
          <v-col cols="12" md="2">
            <export-data-button-menu :label="$t('Export…')" :data="items" :disabled="!selected" />
          </v-col>
        </v-row>
      </template>
    </vc-date-picker>
    <v-data-table v-if="selected" dense :headers="headers" :items="items" :loading="loading" />
  </v-sheet>
</template>

<script>
import ExportDataButtonMenu from '../components/ExportDataButtonMenu'
import PlayoutHistoryTemplateSelect from '../components/PlayoutHistoryTemplateSelect'
import PlayoutHistoryService from '../services/PlayoutHistoryService'

export default {
  name: 'AnalyticsPlayoutHistory',
  components: {
    ExportDataButtonMenu,
    PlayoutHistoryTemplateSelect,
  },
  data: () => ({
    range: {
      start: new Date(),
      end: new Date(),
    },

    selected: false,
    loading: false,
    headers: [],
    items: [],
  }),
  created() {
    this.fetch()
  },
  methods: {
    chooseTemplate(template) {
      this.headers = template.fields
        .sort((a, b) => {
          return a.positition - b.positition
        })
        .map((f) => {
          let value = f.name
          if (f.is_file_md) {
            value = `file.${value}`
          }
          if (value == 'played') {
            value = 'starts'
          }
          return {
            text: f.label,
            value: value,
          }
        })
      this.selected = true
    },

    fetch() {
      this.loading = true
      PlayoutHistoryService.getAll()
        .then((response) => {
          this.items = response.data
          this.loading = false
        })
        .catch(() => {
          this.items = []
          this.loading = false
        })
    },
  },
}
</script>
