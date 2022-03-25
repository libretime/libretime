<template>
  <div>
    <v-app-bar app>
      <p>Hi!</p>
    </v-app-bar>

    <navigation-drawer />

    <router-view />
  </div>
</template>

<script lang="ts">
import { defineComponent } from "vue";
import NavigationDrawer from "@/components/NavigationDrawer.vue";

export default defineComponent({
  name: "DashboardView",
  components: {
    NavigationDrawer,
  },
});
</script>
