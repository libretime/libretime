<template>
  <v-container>
    <v-row>
      <v-col class="col-3">
        <sub-nav :nav-list="side_nav" />
      </v-col>
      <v-col class="col-9">
        <p>Things will go here later</p>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
import SubNav from '../components/Navigation/SubNav.vue'
export default {
  name: 'Help',
  components: { SubNav },
  data() {
    return {
      side_nav: [
        { section: 'GETTING STARTED', items: [{ name: 'Uploading Shows' }] },
        { section: 'USERS', items: [{ name: 'Users' }, { name: 'Permissions' }] },
        {
          section: 'LINKS',
          items: [{ name: 'Docs' }, { name: 'Forums' }, { name: 'Libretime.org' }],
        },
      ],
    }
  },
}
</script>