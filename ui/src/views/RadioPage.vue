<template>
  <v-main>
    <v-container>
      <hello-world />
    </v-container>
  </v-main>
</template>

<script lang="ts">
import { defineComponent } from "vue";

// Components
import HelloWorld from "../components/HelloWorld.vue"
export default defineComponent({
  name: "HomeView",

  components: {
    HelloWorld,
  },
});
</script>
