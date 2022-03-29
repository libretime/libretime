<template>
  <v-container>
    <v-row dense>
      <v-col cols="12">
        <v-card color="#459b8fcc" theme="dark">
          <div class="d-flex flex-no-wrap justify-space-between">
            <div>
              <v-card-title class="text-h5">
                {{ nowPlayingInfo.track }}
              </v-card-title>
              <v-card-subtitle>{{ nowPlayingInfo.artist }}</v-card-subtitle>
              <v-card-actions>
                <v-btn class="ml-2" icon="mdi-play" variant="text"></v-btn>
              </v-card-actions>
            </div>

            <v-avatar class="ma-3" size="125" rounded="0">
              <v-img
                src="https://cdn.vuetifyjs.com/images/cards/halcyon.png"
              ></v-img>
            </v-avatar>
          </div>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script lang="ts">
import { defineComponent } from "vue";

export default defineComponent({
  name: "RadioPlayer",
  setup() {
    const nowPlayingInfo = {
      track: "Burn",
      artist: "Ellie Goulding",
    };
    return { nowPlayingInfo };
  },
});
</script>
