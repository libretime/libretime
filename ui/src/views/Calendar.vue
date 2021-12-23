<template>
  <v-container>
    <v-toolbar dense>
      <show-edit />
      <!-- <v-toolbar-title v-if="$refs.calendar">
        {{ $refs.calendar.title }}
      </v-toolbar-title> -->
      <v-btn icon @click="prev">
        <v-icon>mdi-arrow-left-circle</v-icon>
      </v-btn>
      <v-btn icon @click="next">
        <v-icon>mdi-arrow-right-circle</v-icon>
      </v-btn>
      <v-btn color="gray" @click="setToday">Today</v-btn>
      <v-spacer></v-spacer>
      <v-toolbar-title v-if="$refs.calendar">
        {{ $refs.calendar.title }}
      </v-toolbar-title>
      <v-spacer></v-spacer>
      <v-btn @click="calType = 'day'">Day</v-btn>
      <v-btn @click="calType = 'week'">Week</v-btn>
      <v-btn @click="calType = 'month'">Month</v-btn>
    </v-toolbar>
    <v-sheet height="600">
      <v-calendar
        ref="calendar"
        v-model="value"
        :weekdays="weekday"
        :type="calType"
        :events="events"
        :short-intervals="shortIntervals"
        :short-months="shortMonths"
        :short-weekdays="shortWeekdays"
        :event-overlap-mode="mode"
        :event-overlap-threshold="30"
        :event-color="getEventColor"
      ></v-calendar>
    </v-sheet>
  </v-container>
</template>

<script>
import ShowEdit from '../components/Calendar/ShowEdit.vue'
export default {
  name: 'Calendar',
  components: { ShowEdit },
  data() {
    return {
      calType: 'month',
      focus: '',
    }
  },
  mounted() {
    this.$refs.calendar.checkChange()
  },
  methods: {
    setToday: () => {
      this.focus = ''
    },
    prev: () => {
      this.$refs.calendar.prev()
    },
    next: () => {
      this.$refs.calendar.next()
    },
  },
}
</script>