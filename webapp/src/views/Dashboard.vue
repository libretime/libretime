<template>
  <div>
    <now-playing-bar />

    <navigation-drawer />

    <router-view />
  </div>
</template>

<script lang="ts">
import { defineComponent } from "vue";
import NavigationDrawer from "@/components/NavigationDrawer.vue";
import NowPlayingBar from "@/components/NowPlayingBar.vue"

export default defineComponent({
  name: "DashboardView",
  components: {
    NavigationDrawer,
    NowPlayingBar,
  },
});
</script>
