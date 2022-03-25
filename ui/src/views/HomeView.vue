<template>
  <div>
    <the-welcome />
    <welcome-item />
  </div>
</template>

<script lang="ts">
import { defineComponent } from "vue";

// Components
import TheWelcome from "../components/TheWelcome.vue";
import WelcomeItem from "../components/WelcomeItem.vue";

export default defineComponent({
  name: "HomeView",

  components: {
    TheWelcome,
    WelcomeItem,
  },
});
</script>
