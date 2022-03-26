<template>
  <v-container>
    <div class="libretime_player">
      <div class="libretime_header">
        <p class="station_name">Now Playing</p>
      </div>
      <div class="libretime_box">
        <div class="libretime_button">
          <span v-show="!playing" class="play_button" @click="playBtn()"></span>
          <span v-show="playing" class="stop_button" @click="pauseBtn()"></span>
        </div>
        <div class="now_playing">
          {{ artist }}
          <span>{{ title }}</span>
        </div>
      </div>
      <div style="clear: both"></div>
      <div class="libretime_schedule">
        <p class="libretime_next">Next</p>
        <p class="schedule_list">{{ nextArtist }} - {{ nextTitle }}</p>
      </div>
      <p class="libretime_credit">Powered by Libretime</p>
    </div>
  </v-container>
</template>

<script lang="ts">
import { defineComponent } from "vue";

export default defineComponent({
  name: "NowPlayingWidget",
});
</script>