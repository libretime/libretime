<template>
  <v-container>
    <p>Users!</p>
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
