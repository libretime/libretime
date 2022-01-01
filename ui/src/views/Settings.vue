<template>
  <v-container>
    <v-row>
      <v-col class="col-3">
        <v-list v-for="nav in sideNav" :key="nav" flat nav dense>
          <v-subheader>{{ nav.section }}</v-subheader>
          <v-list-item-group>
            <v-list-item v-for="item in nav.items" :key="item" link :to="item.to">
              {{ item.name }}
            </v-list-item>
          </v-list-item-group>
        </v-list>
      </v-col>
      <v-col class="col-9">
        <router-view />
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
export default {
  name: 'Settings',
  data() {
    return {
      sideNav: [
        {
          section: 'SYSTEM',
          items: [
            { name: 'General', to: '/dashboard/settings/general' },
            { name: 'User', link: '/dashboard/settings/users' },
            { name: 'Stream', link: '/dashboard/settings/streams' },
            { name: 'Track Types', link: '/dashboard/settings/tracktypes' },
            { name: 'Status', link: '/dashboard/settings/status' },
          ],
        },
      ],
    }
  },
}
</script>