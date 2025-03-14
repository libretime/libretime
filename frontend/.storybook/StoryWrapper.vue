<template>
  <v-app :theme="themeName">
    <v-main>
      <slot name="story" />
    </v-main>
  </v-app>
</template>

<script lang="ts">
export default {
  props: {
    themeName: {
      type: String,
      default: "dark",
    },
  },
};
</script>
