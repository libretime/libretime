<template>
  <v-container>
    <v-dialog v-model="dialog" width="700">
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
import api from '../../api'
export default {
  name: 'ShowContents',
  props: { showContents: { type: Object, default: () => {} } },
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