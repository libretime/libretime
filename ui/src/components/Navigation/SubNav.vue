<template>
  <v-container>
    <v-list v-for="nav in navList" :key="nav" flat nav dense>
      <v-subheader>{{ nav.section }}</v-subheader>
      <v-list-item-group>
        <v-list-item v-for="item in nav.items" :key="item" link :to="item.link">
          {{ item.name }}
        </v-list-item>
      </v-list-item-group>
    </v-list>
  </v-container>
</template>

<script>
export default {
  name: 'SubNav',
  props: {
    navList: { type: Object, required: true, default: () => {} },
  },
}
</script>