<template>
  <div>
    <p class="body-2 ma-1 invis">___</p>
    <v-card class="mx-0" outlined>
      <v-row>
        <v-col>
          <v-list-item-title class="body-1 pa-1 ps-2">{{ show }}</v-list-item-title>
        </v-col>
        <v-col class="pe-0 my-1">
          <v-list-item-subtitle class="body-1">
            {{ startTime }} - {{ endTime }}
          </v-list-item-subtitle>
        </v-col>
      </v-row>
      <v-progress-linear value="15" color="blue darken-2"></v-progress-linear>
    </v-card>
    <p class="body-2 ma-1 invis">___</p>
  </div>
</template>

<script>
export default {
  name: 'NowPlayingCard',
  props: {
    show: { type: String, default: 'Offline' },
    startTime: { type: String, default: '' },
    endTime: { type: String, default: '' },
  },
}
</script>

<style lang="sass">
@use '../../assets/styles/base'

.invis
  color: rgba(0, 0, 0, 0)
</style>