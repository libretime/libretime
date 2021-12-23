<template>
  <div class="libretime-vue">
    <v-app dark>
      <!-- Everything goes under here -->
      <radio-page v-show="!userLoggedIn" @try-login="processLogin" />

      <!-- <v-app-bar v-show="userLoggedIn" prominent dense app clipped-left>
        <app-bar />
      </v-app-bar> -->

      <v-main v-show="userLoggedIn">
        <v-container fluid>
          <v-navigation-drawer
            v-show="userLoggedIn"
            :width="200"
            app
            clipped
            permanent
            expand-on-hover
          >
            <main-nav @logout="processLogout" />
          </v-navigation-drawer>
          <router-view></router-view>
        </v-container>
      </v-main>
    </v-app>
  </div>
</template>

<script>
// import AppBar from './components/App/AppBar.vue'
import MainNav from './components/App/MainNav.vue'
import RadioPage from './components/RadioPage/RadioPage.vue'
export default {
  components: {
    MainNav,
    RadioPage,
  },
  data() {
    return {
      stationName: 'WRIR',
      userLoggedIn: false,
    }
  },
  methods: {
    processLogin(username) {
      console.log(username)
      this.userLoggedIn = true
      return
    },
    processLogout() {
      this.userLoggedIn = false
      return
    },
  },
}
</script>

<style lang="sass">
@use './assets/styles/base'

body
  background: base.$background
</style>