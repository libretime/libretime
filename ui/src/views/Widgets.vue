<template>
  <v-row>
    <v-col class="col-3">
      <sub-nav :nav-list="side_nav" />
    </v-col>
    <v-col class="col-9">
      <p>Things will go here later</p>
      <now-playing-widget />
      <weekly-schedule-widget />
    </v-col>
  </v-row>
</template>

<script>
import SubNav from '../components/Navigation/SubNav.vue'
import NowPlayingWidget from '../components/Widgets/NowPlayingWidget.vue'
import WeeklyScheduleWidget from '../components/Widgets/WeeklyScheduleWidget.vue'
export default {
  name: 'Widgets',
  components: { SubNav, NowPlayingWidget, WeeklyScheduleWidget },
  data() {
    return {
      side_nav: [
        {
          section: 'HTML EMBEDS',
          items: [{ name: 'Player' }, { name: 'Now Playing' }, { name: 'Weekly Schedule' }],
        },
        { section: 'WORDPRESS', items: [{ name: 'Plugin' }] },
      ],
    }
  },
}
</script>