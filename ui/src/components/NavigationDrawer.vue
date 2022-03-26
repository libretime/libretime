<template>
  <v-navigation-drawer app permanent>
    <p>Yo</p>
    <v-list :items="items" />
    <disk-quota-card />
  </v-navigation-drawer>
</template>

<script lang="ts">
import { defineComponent } from "vue";
import DiskQuotaCard from "./DiskQuotaCard.vue";

export default defineComponent({
  name: "NavigationDrawer",

  components: {
    DiskQuotaCard,
  },

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
