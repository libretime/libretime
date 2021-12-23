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

<style lang="sass">
@use '../../assets/styles/base'
$box-shadow: 0 3px 5px rgba(0, 0, 0, 0.1) inset, 0 1px 0 rgba(255, 255, 255, 0.1), 0 0 1px #000 inset

.libretime_player
  background: base.$background
  font-family: base.$font-stack
  width: 270px
  height: 191px
  position: relative
  color: #fff
  border-radius: 4px
  box-shadow: $box-shadow
  .libretime_header
    background: base.$background
    box-shadow: $box-shadow
    height: 37px
    // border-radius: 4px
    .station_name
      font-size: 14px
      padding-top: 10px
      padding-left: 20px
      white-space: nowrap
      overflow: hidden
      text-overflow: ellipsis
      padding-right: 30px
  .libretime_box
    margin-top: 15px
    float: left
    width: 100%
    height: 52px
  .libretime_box
    .libretime_button
      // text-indent: -9999px;
      border-radius: 2px
      background: rgb(100, 100, 100)
      background: linear-gradient(top, rgba(107, 107, 107, 1) 0%, rgba(88, 88, 88, 1) 100%)
      // background: linear-gradient(to bottom, rgba(107, 107, 107, 1) 0%, rgba(88, 88, 88, 1) 100%)
      box-shadow: 0px 1px 0px rgba(255, 255, 255, 0.2) inset
      width: 47px
      height: 47px
      float: left
      cursor: pointer
      margin-left: 20px
      margin-right: 15px
      // filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#646464', endColorstr='#585858',GradientType=0 );
      .play_button
        display: block
        background: url('../../assets/img/play_button.png') center no-repeat
        width: 47px
        height: 47px
      .stop_button
        display: block
        background: url('../../assets/img/pause_button.png') center no-repeat
        width: 47px
        height: 47px
    .libretime_button:hover
      background: rgb(147, 147, 147)
      background: linear-gradient(top, rgba(147, 147, 147, 1) 0%, rgba(117, 117, 117, 1) 100%)
      background: linear-gradient(to bottom, rgba(147, 147, 147, 1) 0%, rgba(117, 117, 117, 1) 100%)
      // filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#939393', endColorstr='#757575',GradientType=0 )
    .now_playing
      margin-top: 3px
      margin-left: 15px
      margin-right: 15px
      display: block
      font-size: 14px
      color: #fff
      width: 170px
      text-overflow: ellipsis
      white-space: nowrap
      overflow: hidden
      span
        display: block
        color: #aaaaaa
        width: 170px
        text-overflow: ellipsis
        white-space: nowrap
        overflow: hidden

.libretime_credit
  margin: 6px 20px
  color: base.$libretime-orange
  font-size: 12px
  float: right
  // text-decoration: none

// .libretime_volume
//   padding: 10px 0px 15px 0px
//   clear: both
//   .volume_control
//     margin-left: 55px
//     float: left
//   .mute
//     background: url('../../assets/img/mute.png') center no-repeat
//     display: block
//     margin-top: -4px
//     width: 15px
//     height: 15px
//     cursor: pointer

.libretime_volume_bar
  border-color: #262526 #262526 #5e5e5e
  border-style: solid
  border-width: 1px
  background-color: #393939
  width: auto
  height: 5px
  cursor: pointer
  margin-left: 80px
  margin-right: 40px

.libretime_volume_bar_value
  background-color: #ff9122
  width: 0px
  height: 5px

$schedule-border: 1px solid rgba(255, 255, 255, 0.1)
.libretime_schedule
  margin: 10px 20px 5px 20px
  padding-top: 10px
  font-size: 14px
  color: #aaaaaa
  border-top: $schedule-border
  border-bottom: $schedule-border
  padding-bottom: 0px

.libretime_next
  float: left
  margin: 0px
  margin-top: 1px

.schedule_list
  list-style: none
  padding-left: 0px
  padding-bottom: 10px
  margin-top: 1px
  margin-left: 60px
  margin-bottom: 0px
  line-height: 150%
  height: 20px
  li
    white-space: nowrap
    overflow: hidden
    text-overflow: ellipsis
</style>