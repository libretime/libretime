<template>
  <v-menu offset-y>
    <template #activator="{ on, attrs }">
      <v-btn v-bind="attrs" dark color="primary" v-on="on">{{ label }}</v-btn>
    </template>
    <v-list>
      <v-list-item v-for="t in types" :key="t.type">
        <vue-blob-json-csv :file-type="t.type" :file-name="fileName" :data="data">
          <v-list-item-title :class="`export-data-menu-button-${t.type}`">{{
            t.type.toUpperCase()
          }}</v-list-item-title>
        </vue-blob-json-csv>
      </v-list-item>
    </v-list>
  </v-menu>
</template>

<script>
/**
 * Download data provided as CSV or JSON.
 */
export default {
  name: 'ExportDataButtonMenu',
  props: {
    /**
     * Label for the button.
     */
    label: {
      type: String,
      default: 'Export…',
    },
    /**
     * Name for the generated file.
     */
    fileName: {
      type: String,
      default: 'download',
    },
    /**
     * Data to include in the file.
     */
    data: {
      type: Array,
      required: true,
    },
  },
  data: () => ({
    types: [
      {
        type: 'csv',
      },
      {
        type: 'json',
      },
    ],
  }),
}
</script>
