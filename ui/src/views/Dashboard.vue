<template>
  <div>
    <v-app-bar app>
      <p>Hi!</p>
    </v-app-bar>

    <v-navigation-drawer app permanent>
      <p>Yo</p>
      <v-list :items="items" />
    </v-navigation-drawer>
    <router-view />
  </div>
</template>

<script lang="ts">
import { defineComponent } from "vue";

export default defineComponent({
  name: "HomeView",

  data: () => ({
    items: [
      {
        title: "Foo",
        value: "foo",
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
