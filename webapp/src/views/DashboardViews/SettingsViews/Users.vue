<template>
  <v-container>
    <v-row>
      <v-col>
        <p class="text-h6">Users</p>
      </v-col>
      <v-spacer></v-spacer>
      <v-col>
        <v-btn color="grey" plain> Save </v-btn>
      </v-col>
    </v-row>
    <add-user />
  </v-container>
</template>

<script lang="ts">
import { defineComponent } from "vue";
import AddUser from "@/components/Settings/AddUser.vue";

export default defineComponent({
  name: "UsersView",
  components: {
    AddUser,
  },
});
</script>
