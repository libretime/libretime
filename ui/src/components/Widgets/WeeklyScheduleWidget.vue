<template>
  <v-container>
    <v-card width="750" class="schedule">
      <ul class="tabs">
        <li v-for="day in days" :key="day">
          <p @click="switchDay(day.dateNum)">
            {{ day.dayName }} <br />
            <span>{{ day.dateNum }}</span>
          </p>
        </li>
      </ul>
      <div class="shows">
        <div v-show="noShows" class="empty-schedule">
          <p>Looks like there are no shows scheduled on this day.</p>
        </div>
        <div v-for="show in selDay" v-show="!noShows" :key="show" class="row">
          <div class="time_grid">{{ show.startTime }} - {{ show.endTime }}</div>
          <div class="name_grid">{{ show.name }}</div>
        </div>
      </div>
      <p class="libretime-credit">Powered by Libretime</p>
    </v-card>
  </v-container>
</template>

<script>
import api from '../../devapi'
export default {
  name: 'WeeklyScheduleWidget',
  data() {
    return {
      noShows: false,
      days: [
        { dayName: 'Mon', dateNum: '11', shows: [] },
        { dayName: 'Tues', dateNum: '12', shows: [] },
        { dayName: 'Wed', dateNum: '13', shows: [] },
        { dayName: 'Thur', dateNum: '14', shows: [] },
        { dayName: 'Fri', dateNum: '15', shows: [] },
        { dayName: 'Sat', dateNum: '16', shows: [] },
        { dayName: 'Sun', dateNum: '17', shows: [] },
      ],
      apiData: [
        { dayName: 'Mon', dateNum: '11', shows: [] },
        { dayName: 'Tues', dateNum: '12', shows: [] },
        { dayName: 'Wed', dateNum: '11', shows: [] },
        { dayName: 'Thur', dateNum: '13', shows: [] },
        { dayName: 'Fri', dateNum: '14', shows: [] },
        { dayName: 'Sat', dateNum: '15', shows: [] },
        { dayName: 'Sun', dateNum: '16', shows: [] },
      ],
      selDay: [
        {
          name: 'Democracy Now',
          startTime: '800',
          endTime: '900',
        },
        {
          name: 'Big Picture Science',
          startTime: '900',
          endTime: '1000',
        },
      ],
    }
  },
  methods: {
    getSchedule: () => {
      // Get schedule from API
      api
        .get('/show-instances/')
        .then((response) => (this.apiData = response.data))
        .catch((err) => console.log(err))
    },
    switchDay: (num) => {
      this.apiData.every((element) => {
        if (element.dateNum === num) {
          this.selDay = element.shows
          return false // exits loop
        } else return true // continues loop
      })
    },
    noShowsCheck: (obj) => {
      if (obj.length == 0) {
        this.noShows = true
      } else {
        this.noShows = false
      }
    },
  },
}
</script>

<style lang="sass">
@use '../../assets/styles/base'

body
  margin: 0px

.schedule
  .tabs
    list-style: none
    padding-left: 0px
    margin: 0px
    background: base.$background
    li
      width: 103px
      height: 80px
      display: inline-block
      font-size: 14px
      padding: 15px
      box-sizing: border-box
      cursor: pointer
      color: #fff
      span
        font-size: 30px
        display: block
        color: base.$libretime-orange
    li.active
      background: #459B8F
    li.active:hover
      // background: #459B8F
      background: rgba(69, 155, 143, 0.6)
  .shows
    background: rgba(0, 0, 0, 0.3)
    max-height: 700px
    transition: max-height 2s ease
    overflow-y: auto
    overflow-x: hidden
    text-overflow: ellipsis
    // transition-delay: 1s
    // height: 50%
    // overflow-y: auto
    // max-height: 700px
    font-size: 17px
    text-align: left
    text-transform: uppercase
    padding: 30px 40px
    .row
      padding-top: 10px
      padding-bottom: 10px
    .time_grid
      padding-right: 10px
      width: 30%
      font-weight: 300
      color: base.$light-grey
      display: inline-block
    .name_grid
      overflow: hidden
      padding-left: 10px
      width: 67%
      display: inline-block
      vertical-align: middle
    .empty-schedule
      text-transform: none
      text-align: center
  .libretime-credit
    margin: 6px 20px
    color: base.$libretime-orange
    font-size: 12px
    float: right
    // text-decoration: none

@media (max-width: 730px)
  .tab_content
    margin-top: 0px
    width: auto
    max-width: 100%
    margin-left: auto
    left: 10px
    right: 10px
  .schedule
    .tabs
      li
        width: 64px
        height: 64px
        padding: 6px

@media (max-width: 630px)
.schedule_item
  div.time_grid
    width: 37%
  div.name_grid
    width: 58%

@media (max-width: 530px)
  .schedule_item
    padding: 10px 20px
    font-size: 14px
    div.time_grid
      width: 35%
    div.name_grid
      width: 60%

@media (max-width: 500px)
  .schedule_item
    div.time_grid
      width: 40%
    div.name_grid
      width: 55%

@media (max-width: 400px)
  .schedule_item
    div.time_grid
      width: 95%
    div.name_grid
      width: 95%
</style>