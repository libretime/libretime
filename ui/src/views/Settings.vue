<template>
  <v-container>
    <v-row>
      <v-col class="col-3">
        <v-list v-for="nav in sideNav" :key="nav" flat nav dense>
          <v-subheader>{{ nav.section }}</v-subheader>
          <v-list-item-group>
            <v-list-item v-for="item in nav.items" :key="item" link @click="switchTab(item.tab)">
              {{ item.name }}
            </v-list-item>
          </v-list-item-group>
        </v-list>
      </v-col>
      <v-col class="col-9">
        <general />
        <users />
        <track-types />
        <stream />
        <status />
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
import General from '../components/Settings/General.vue'
import Stream from '../components/Settings/Stream.vue'
import Users from '../components/Settings/Users.vue'
import TrackTypes from '@/components/Settings/TrackTypes.vue'
import Status from '@/components/Settings/Status.vue'
export default {
  name: 'Settings',
  components: { General, Users, Stream, TrackTypes, Status },
  data() {
    return {
      display: 'general',
      sideNav: [
        {
          section: 'SYSTEM',
          items: [
            { name: 'General', tab: 'general' },
            { name: 'User', link: 'users' },
            { name: 'Stream Settings', link: 'stream' },
          ],
        },
      ],
    }
  },
  methods: {
    switchTab: (tab) => {
      this.display = tab
    },
  },
}
</script>