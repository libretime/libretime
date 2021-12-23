<template>
  <div>
    <p class="body-2 ma-1"><b>Previous:</b> {{ npPrev }}</p>
    <v-card class="mx-0" outlined>
      <v-row>
        <v-col>
          <v-list-item-title class="body-1 pa-1 ps-2">{{ npInfo }}</v-list-item-title>
        </v-col>
        <v-col class="pe-0 my-1">
          <v-list-item-subtitle class="body-1"><strong>1:23</strong> 4:00 </v-list-item-subtitle>
        </v-col>
      </v-row>
      <v-progress-linear value="15" color="orange darken-2"></v-progress-linear>
    </v-card>
    <p class="body-2 ma-1"><b>Next:</b> {{ npNext }}</p>
  </div>
</template>

<script>
export default {
  name: 'NowPlayingCard',
  props: {
    npInfo: { type: String, default: 'Offline' },
    npPrev: { type: String, default: 'Offline' },
    npNext: { type: String, default: 'Offline' },
  },
}
</script>

<style lang="sass">
@use '../../assets/styles/base'
</style>