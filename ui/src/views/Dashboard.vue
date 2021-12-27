<template>
  <div>
    <v-navigation-drawer :width="200" app permanent expand-on-hover>
      <main-nav />
    </v-navigation-drawer>
    <router-view />
  </div>
</template>

<script>
import MainNav from '../components/App/MainNav.vue'
export default {
  name: 'Dashboard',
  components: { MainNav },
}
</script>