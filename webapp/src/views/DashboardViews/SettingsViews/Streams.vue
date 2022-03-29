<template>
  <v-container>
    <v-row>
      <v-col>
        <p class="text-h6">Stream Settings</p>
      </v-col>
      <v-spacer></v-spacer>
      <v-col>
        <v-btn color="grey" plain> Save </v-btn>
      </v-col>
    </v-row>
    <v-row>
      <v-col>
        <p class="text-h6">Global</p>
        <v-checkbox
          v-model="options.global.hardwareOut"
          :label="`Hardware Audio Output`"
        />
        <v-checkbox
          v-model="options.global.icecastMeta"
          :label="`Icecast Vorbis Metadata`"
        />
        <v-text-field
          v-model="options.global.offAirMeta"
          :label="`Off Air Metadata`"
        />
        <v-checkbox
          v-model="options.global.replayGain"
          :label="`Enable Replay Gain`"
        />
        <v-slider
          v-model="options.global.replayGainDB"
          :min="-10"
          :max="10"
          :step="1"
          thumb-label
        />
        <p class="text-h6">Live Broadcasting</p>
        <v-checkbox v-model="options.live.autoOff" :label="`Auto Switch Off`" />
        <v-checkbox v-model="options.live.autoOn" :label="`Auto Switch On`" />
        <v-text-field
          v-model="options.live.transTime"
          :label="`Switch Transition Fade (s)`"
        />
      </v-col>
      <v-col>
        <p class="text-h6">Output Streams</p>
        <v-container class="pa-1" fluid>
          <v-radio-group v-model="options.outputDefaults">
            <v-radio label="Default Streaming" value="default"></v-radio>
            <v-radio label="Custom Streaming" value="custom"></v-radio>
          </v-radio-group>
        </v-container>
        <v-expansion-panels>
          <v-expansion-panel>
            <v-expansion-panel-title> Stream 1 </v-expansion-panel-title>
            <v-expansion-panel-text>
              <output-stream-card />
            </v-expansion-panel-text>
          </v-expansion-panel>
          <v-expansion-panel>
            <v-expansion-panel-title> Stream 2 </v-expansion-panel-title>
            <v-expansion-panel-text>
              <output-stream-card />
            </v-expansion-panel-text>
          </v-expansion-panel>
          <v-expansion-panel>
            <v-expansion-panel-title> Stream 3 </v-expansion-panel-title>
            <v-expansion-panel-text>
              <output-stream-card />
            </v-expansion-panel-text>
          </v-expansion-panel>
        </v-expansion-panels>
      </v-col>
    </v-row>
  </v-container>
</template>

<script lang="ts">
import { defineComponent, reactive } from "vue";
import OutputStreamCard from "@/components/Settings/OutputStreamCard.vue";

export default defineComponent({
  name: "StreamSettingsView",
  components: {
    OutputStreamCard,
  },
  setup() {
    const options = reactive({
      outputDefaults: "default",
      global: {
        hardwareOut: false,
        icecastMeta: false,
        offAirMeta: "Libretime - Offline",
        replayGain: false,
        replayGainDB: 0,
      },
      live: {
        autoOff: false,
        autoOn: false,
        transTime: "1",
      },
    });
    return { options };
  },
});
</script>
