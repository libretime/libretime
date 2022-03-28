<template>
  <v-container>
    <sub-nav subtitle="Settings" :items="items" />
    <router-view />
  </v-container>
</template>

<script lang="ts">
import { defineComponent } from "vue";
import SubNav from "@/components/SubNav.vue";

export default defineComponent({
  name: "SettingsParentView",
  component: {
    SubNav,
  },
  setup() {
    const items = [
      {
        title: "General",
        to: "/dashboard/settings/general",
      },
      {
        title: "My Profile",
        to: "/dashboard/settings/profile",
      },
      {
        title: "Users",
        to: "/dashboard/settings/users",
      },
      {
        title: "Streams",
        to: "/dashboard/settings/streams",
      },
      {
        title: "Status",
        to: "/dashboard/settings/status",
      },
    ];
    return { items };
  },
});
</script>
