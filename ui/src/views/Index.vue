<template>
  <radio-page v-show="!userLoggedIn" @try-login="processLogin" />
</template>

<script>
import RadioPage from '../components/RadioPage/RadioPage.vue'
export default {
  name: 'Index',
  components: { RadioPage },
}
</script>

<style></style>