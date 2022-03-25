<template>
  <hello-world />
</template>

<script lang="ts">
import { defineComponent } from "vue";

// Components
import HelloWorld from "../components/HelloWorld.vue";

export default defineComponent({
  name: "AboutView",

  components: {
    HelloWorld,
  },
});
</script>

<style>
@media (min-width: 1024px) {
  .about {
    min-height: 100vh;
    display: flex;
    align-items: center;
  }
}
</style>
