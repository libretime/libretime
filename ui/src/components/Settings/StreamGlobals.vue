<template>
  <v-card class="mx-auto" max-width="300">
    <v-card-text>
      <v-card-title>Global</v-card-title>
      <v-form>
        <v-checkbox v-model="hardwareAudio" label="Hardware Audio Output" />
        <v-select v-model="outputType" :items="selectItems.outputType" label="Stream Codec" />
        <v-checkbox v-model="icecastMeta" label="Icecast Vorbis Metadata" />
        <v-select v-model="streamLabel" :items="selectItems.streamLabel" label="Stream Label" />
        <v-text-field v-model="offAirMeta" label="Off Air Metadata" />
        <v-checkbox v-model="replayGain" label="Enable Replay Gain" />
        <v-slider v-model="replayGainDB" :min="gainMin" :max="gainMax" thumb-label />
      </v-form>
    </v-card-text>
    <!-- <v-card-actions>
      <v-btn text color="deep-purple accent-4"> Enable </v-btn>
      <v-btn text color="deep-purple accent-4"> Save </v-btn>
    </v-card-actions> -->
  </v-card>
</template>

<script>
export default {
  name: 'StreamGlobals',
  data() {
    return {
      hardwareAudio: false,
      outputType: 'ALSA',
      icecastMeta: false,
      streamLabel: 'Artist - Title',
      offAirMeta: 'Libretime - offline',
      replayGain: true,
      replayGainDB: 0,
      gainMin: -26,
      gainMax: 10,
      selectItems: {
        outputType: ['ALSA'],
        streamLabel: ['Artist - Title', 'Show - Artist - Title', 'Station name - Show name'],
      },
    }
  },
}
</script>

<style lang="sass"></style>