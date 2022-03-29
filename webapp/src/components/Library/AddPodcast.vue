<template>
  <v-container>
    <v-dialog v-model="dialog" width="500">
      <template #activator="{ on, attrs }">
        <v-btn dark v-bind="attrs" v-on="on">Add</v-btn>
      </template>
      <v-card>
        <v-card-title class="text-h5">Add Podcast</v-card-title>
        <v-form>
          <v-text-field label="Podcast URL"></v-text-field>
        </v-form>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="orange darken-3" text @click="state.dialog = false"
            >Submit</v-btn
          >
        </v-card-actions>
      </v-card>
    </v-dialog>
  </v-container>
</template>

<script lang="ts">
import { defineComponent, reactive } from "vue";

export default defineComponent({
  name: "AddPodcastDialog",
  setup() {
    const state = reactive({ dialog: false });
    return {
      state,
    };
  },
});
</script>
