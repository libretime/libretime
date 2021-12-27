<template>
  <v-row class="app-bar">
    <v-col class="col-1">
      <v-container>
        <v-img
          class="align-sm-center child-flex"
          src="../../assets/logo.png"
          max-height="100"
          max-width="100"
          contain
        />
      </v-container>
    </v-col>
    <v-col class="col-4">
      <now-playing-card
        :np-info="'The Replacements - Nobody'"
        :np-prev="'Blondie - Heart of Glass'"
        :np-next="'Tom Petty - Even The Losers'"
      />
    </v-col>
    <v-col class="col-4">
      <current-show :show="'Classic Rock Hits'" :start-time="1300" :end-time="1500" />
    </v-col>
    <v-col>
      <source-streams />
    </v-col>
    <v-col>
      <station-clock />
    </v-col>
  </v-row>
</template>

<script>
import CurrentShow from '../NowPlaying/CurrentShow.vue'
import NowPlayingCard from '../NowPlaying/NowPlayingCard.vue'
import SourceStreams from './SourceStreams.vue'
import StationClock from './StationClock.vue'
export default {
  name: 'AppBar',
  components: {
    NowPlayingCard,
    CurrentShow,
    StationClock,
    SourceStreams,
  },
}
</script>

<style lang="sass">
.app-bar
  background: rgb(55,55,55)
  background: linear-gradient(0deg, rgba(55,55,55,1) 0%, rgba(99,99,99,1) 100%)
</style>