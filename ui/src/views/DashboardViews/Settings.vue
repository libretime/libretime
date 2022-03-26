<template>
  <v-container>
    <p>Settings!</p>
    <router-view />
  </v-container>
</template>

<script lang="ts">
import { defineComponent } from "vue";

export default defineComponent({
  name: "SettingsParentView",
});
</script>
