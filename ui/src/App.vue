<template>
  <div class="libretime-vue">
    <v-app dark>
      <v-main>
        <v-container fluid>
          <router-view />
        </v-container>
      </v-main>
    </v-app>
  </div>
</template>

<script>
export default {
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
@use './styles/globals.sass'
body
  background: globals.$background
</style>