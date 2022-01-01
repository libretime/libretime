<template>
  <v-container>
    <v-dialog v-model="dialog" width="700">
      <template #activator="{ on, attrs }">
        <v-list-item v-bind="attrs" link v-on="on">
          <v-list-item-icon>
            <v-icon>mdi-upload</v-icon>
          </v-list-item-icon>
          <v-list-item-title>Show Contents</v-list-item-title>
        </v-list-item>
      </template>
      <v-card>
        <v-card-title class="text-h6">Show Contents</v-card-title>
        <v-data-table
          :headers="headers"
          :items="showContents"
          :items-per-page="all"
          class="elevation-1 dark"
        ></v-data-table>
        <v-divider></v-divider>
        <v-progress-linear value="39"></v-progress-linear>
      </v-card>
    </v-dialog>
  </v-container>
</template>

<script>
import useGetShowContents from '@/composables/useGetShowContents.js'
export default {
  props: { showContents: { type: Object, default: () => {} } },
  setup() {
    const { showContents } = useGetShowContents()
    return showContents
  },
  data() {
    return {
      dialog: false,
      headers: [
        { text: 'Title', value: 'title' },
        { text: 'Artist', value: 'artist' },
        { text: 'Album', value: 'album' },
        { text: 'Duration', value: 'duration' },
      ],
    }
  },
}
</script>