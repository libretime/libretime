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
@use '../../styles/Widgets.sass'
</style>