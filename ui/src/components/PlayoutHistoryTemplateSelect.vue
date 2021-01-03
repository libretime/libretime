<template>
  <v-select
    dense
    item-text="name"
    item-value="id"
    :label="label"
    :loading="loading"
    :items="templates"
    @change="emitChangeEvent($event)"
  />
</template>

<script>
import PlayoutHistoryTemplateService from '../services/PlayoutHistoryTemplateService'

/**
 * `select` tag that gets populated from the `/api/v2/playout-history-template` API endpoint.
 *
 * The component emits a `change` event containing the complete template if one is selected.
 *
 * ```vue
 * <playout-history-template-select @change="onChange($event)" />
 * ```
 */
export default {
  name: 'PlayoutHistoryTemplateSelect',
  props: {
    label: {
      type: String,
      default: 'Template',
    },
  },
  data: () => ({
    loading: true,
    templates: [],
    err: null,
  }),
  created() {
    this.fetch()
  },
  methods: {
    fetch() {
      PlayoutHistoryTemplateService.getAll()
        .then((response) => {
          this.templates = response.data
          this.loading = false
        })
        .catch((e) => {
          this.templates = []
          this.loading = false
          this.err = e
        })
    },

    selectedTemplate(id) {
      return this.templates.find((t) => t.id === id)
    },

    emitChangeEvent($event) {
      this.$emit('change', this.selectedTemplate($event))
    },
  },
}
</script>
