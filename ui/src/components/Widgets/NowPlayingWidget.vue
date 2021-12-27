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

<script>
import { Howl } from 'howler'
import api from '../../api'
import '@/styles/Widgets.sass'

export default {
  name: 'NowPlayingWidget',
  data() {
    return {
      playing: false,
      player: {},
      streamURLs: [],
      title: 'Nobody',
      artist: 'The Replacements',
      nextTitle: 'Shining Bright',
      nextArtist: 'Echosmith',
    }
  },
  beforeMount: () => {
    // Get list of streaming mounts from API
    let apiData = {}
    api
      .get('/mount-names/')
      .then((response) => (apiData = response.data))
      .catch((error) => console.log(error))
    // Clean API data, insert into array of URLs (streamURLs)
    apiData.forEach((element) => {
      this.streamURLs.push('http://192.168.64.43:8080' + element.mount_name)
    })
    // Create Howler instance
    this.player = new Howl({
      src: this.streamURLs,
      html5: true,
      autoplay: false,
      volume: 0.5,
    })
  },
  methods: {
    playBtn: () => {
      this.player.play()
      this.player.on('play', () => {
        this.playing = true
      })
    },
    pauseBtn: () => {
      this.player.pause()
      this.player.on('stop', () => {
        this.playing = false
      })
    },
  },
}
</script>
