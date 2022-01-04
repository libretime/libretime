<template>
  <v-container>
    <p class="text-h5">Stream Settings</p>
    <v-form>
      <v-container fluid>
        <global-settings />
        <v-row dense>
          <v-col :cols="6">
            <output-streams />
            <br />
            <output-streams />
          </v-col>
          <v-col :cols="6">
            <output-streams />
            <br />
            <output-streams />
          </v-col>
        </v-row>
      </v-container>
    </v-form>
  </v-container>
</template>

<script>
import GlobalSettings from '@/components/Settings/StreamGlobals.vue'
import OutputStreams from '@/components/Settings/OutputStreams.vue'
export default {
  name: 'StreamSettings',
  components: { GlobalSettings, OutputStreams },
  data() {
    return {
      streams: [{}],
    }
  },
}
</script>