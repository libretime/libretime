<template>
  <v-dialog v-model="dialog" persistent max-width="600px">
    <template #activator="{ on, attrs }">
      <v-btn color="orange darken-3" dark v-bind="attrs" v-on="on">Add Show</v-btn>
    </template>
    <v-card>
      <v-card-title>
        <span class="text-h5">Show</span>
      </v-card-title>
      <v-container>
        <!-- <template #extension> -->
        <v-tabs v-model="showEditTabs" centered>
          <v-tab>Basics</v-tab>
          <v-tab>DJ</v-tab>
          <v-tab>Stream</v-tab>
          <v-tab>Advanced</v-tab>
        </v-tabs>
        <!-- </template> -->
        <v-tabs-items v-model="showEditTabs">
          <v-tab-item>Basics</v-tab-item>
          <v-tab-item>Hi</v-tab-item>
          <v-tab-item>There</v-tab-item>
          <v-tab-item>Somebody</v-tab-item>
        </v-tabs-items>
        <v-btn color="orange darken-1" text @click="dialog = false">Save</v-btn>
      </v-container>
    </v-card>
  </v-dialog>
</template>

<script>
export default {
  name: 'ShowEdit',
  data() {
    return {
      dialog: false,
      tab: null,
      navList: ['Basics', 'Live Stream', 'DJs', 'Advanced'],
    }
  },
}
</script>