<template>
  <v-menu offset-y>
      <template v-slot:activator="{ on, attrs }">
        <v-btn
          color="primary"
          dark
          v-bind="attrs"
          v-on="on"
        >
          {{ $t('Export…') }}
        </v-btn>
      </template>
      <v-list>
        <v-list-item>
          <v-list-item-title>CSV</v-list-item-title>
        </v-list-item>
        <v-list-item>
          <v-list-item-title>JSON</v-list-item-title>
        </v-list-item>
      </v-list>
  </v-menu>
</template>

<script>
  export default {
    name: 'ExportDataButtonMenu',
    props: {
      data: String,
    }
  }
</script>
