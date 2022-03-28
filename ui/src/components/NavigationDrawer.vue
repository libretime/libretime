<template>
  <v-navigation-drawer app permanent width="200">
    <v-btn color="primary" block> Upload </v-btn>
    <p class="text-overline">Dashboard</p>
    <v-list dense nav>
      <v-list-item
        v-for="dashnav in dashnavitems"
        :key="dashnav.title"
        :to="dashnav.to"
        link
      >
        <v-list-item-icon>
          <v-icon>{{ dashnav.icon }}</v-icon>
        </v-list-item-icon>
        <v-list-item-content>
          <v-list-item-title>{{ dashnav.title }}</v-list-item-title>
        </v-list-item-content>
      </v-list-item>
    </v-list>
    <v-divider />
    <v-list dense nav>
      <v-list-item v-for="nav in navitems" :key="nav.title" :to="nav.to" link>
        <v-list-item-icon>
          <v-icon>{{ nav.icon }}</v-icon>
        </v-list-item-icon>
        <v-list-item-content>
          <v-list-item-title>{{ nav.title }}</v-list-item-title>
        </v-list-item-content>
      </v-list-item>
    </v-list>
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
  setup() {
    const dashnavitems = [
      {
        title: "Tracks",
        icon: "mdi-album",
        to: "/dashboard/tracks",
      },
      {
        title: "Playlists",
        icon: "mdi-playlist-music",
        to: "/dashboard/playlists",
      },
      {
        title: "SmartBlocks",
        icon: "mdi-playlist-star",
        to: "/dashboard/smartblocks",
      },
      {
        title: "Webstreams",
        icon: "mdi-radio-tower",
        to: "/dashboard/webstreams",
      },
      {
        title: "Podcasts",
        icon: "mdi-podcast",
        to: "/dashboard/podcasts",
      },
    ];
    const navitems = [
      {
        title: "Calendar",
        icon: "mdi-calendar-month",
        to: "/dashboard/calendar",
      },
      {
        title: "Widgets",
        icon: "mdi-widgets",
        to: "/dashboard/widgets",
      },
      {
        title: "Settings",
        icon: "mdi-tune",
        to: "/dashboard/settings",
      },
      {
        title: "Playout History",
        icon: "mdi-home-analytics",
        to: "/dashboard/playouthistory",
      },
      {
        title: "Help",
        icon: "mdi-help-circle",
        to: "/dashboard/help",
      },
    ];
    return { dashnavitems, navitems };
  },
});
</script>
