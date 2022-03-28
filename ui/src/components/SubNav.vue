<template>
  <v-navigation-drawer permanent width="200">
    <p class="text-overline">{{ subtitle }}</p>
    <v-list dense nav>
      <v-list-item v-for="nav in items" :key="nav.title" :to="nav.to" link>
        <v-list-item-content>
          <v-list-item-title>{{ nav.title }}</v-list-item-title>
        </v-list-item-content>
      </v-list-item>
    </v-list>
  </v-navigation-drawer>
</template>

<script lang="ts">
import { defineComponent } from "vue";

export default defineComponent({
  name: "SubNav",
  props: ["subtitle", "items"],
});
</script>
