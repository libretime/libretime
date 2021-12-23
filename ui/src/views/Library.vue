<template>
  <v-row>
    <v-col class="pa-1 col-6">
      <v-container>
        <track-library />
      </v-container>
    </v-col>
    <v-col class="pa-1 col-6">
      <v-container>
        <track-library />
      </v-container>
    </v-col>
  </v-row>
</template>

<script>
import TrackLibrary from '../components/Library/TrackLibrary.vue'
export default {
  name: 'Library',
  components: { TrackLibrary },
}
</script>