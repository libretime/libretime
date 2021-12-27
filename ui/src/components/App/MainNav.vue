<template>
  <!-- <v-col md="4"> -->
  <v-card>
    <v-list-item class="px-2">
      <v-list-item-avatar>
        <v-img src="../../assets/logo.svg" />
      </v-list-item-avatar>
    </v-list-item>
    <v-list dense nav>
      <upload-shows />
      <v-list-item v-for="item in navItemsTop" :key="item.title" :to="item.link" link>
        <v-list-item-icon>
          <v-icon>{{ item.icon }}</v-icon>
        </v-list-item-icon>
        <v-list-item-title>{{ item.title }}</v-list-item-title>
      </v-list-item>
      <v-divider class="mx-1"></v-divider>
      <v-list-item v-for="item in navItemsBot" :key="item.title" :to="item.link" link>
        <v-list-item-icon>
          <v-icon>{{ item.icon }}</v-icon>
        </v-list-item-icon>
        <v-list-item-title>{{ item.title }}</v-list-item-title>
      </v-list-item>
      <v-divider class="mx-1"></v-divider>
      <v-list-item link @click="logout">
        <v-list-item-icon><v-icon>mdi-logout</v-icon></v-list-item-icon>
        <v-list-item-title>Logout</v-list-item-title>
      </v-list-item>
    </v-list>
  </v-card>
  <!-- </v-col> -->
</template>

<script>
import UploadShows from '../Library/UploadShows.vue'

export default {
  name: 'MainNav',
  components: { UploadShows },
  data() {
    return {
      navItemsTop: [
        { title: 'Library', link: '/dashboard/library', icon: 'mdi-album' },
        { title: 'Calendar', link: '/dashboard/calendar', icon: 'mdi-calendar-month' },
      ],
      navItemsBot: [
        { title: 'Widgets', link: '/dashboard/widgets', icon: 'mdi-toy-brick' },
        { title: 'Settings', link: '/dashboard/settings', icon: 'mdi-progress-wrench' },
        { title: 'Analytics', link: '/dashboard/playouthistory', icon: 'mdi-poll' },
        { title: 'Help', link: '/dashboard/help', icon: 'mdi-help-box' },
      ],
    }
  },
  methods: {
    logout() {
      this.$emit('logout')
      return
    },
  },
}
</script>

<style lang="sass">


body
  background: #333333
</style>