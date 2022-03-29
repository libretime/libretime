<template>
  <v-container>
    <v-row>
      <v-col>
        <p class="text-h6">My Profile</p>
        <v-btn color="grey" plain> Save </v-btn>
      </v-col>
    </v-row>
  </v-container>
</template>

<script lang="ts">
import { defineComponent } from "vue";

export default defineComponent({
  name: "MyProfileView",
});
</script>
