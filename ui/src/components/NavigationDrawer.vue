<template>
  <v-navigation-drawer app permanent>
    <p>Yo</p>
    <v-list :items="items" />
  </v-navigation-drawer>
</template>

<script lang="ts">
import { defineComponent } from "vue";

export default defineComponent({
  name: "NavigationDrawer",

  data: () => ({
    items: [
      {
        title: "Food",
        value: "food",
      },
      {
        title: "Bar",
        value: "bar",
      },
      {
        title: "Fizz",
        value: "fizz",
      },
      {
        title: "Buzz",
        value: "buzz",
      },
    ],
  }),
});
</script>
