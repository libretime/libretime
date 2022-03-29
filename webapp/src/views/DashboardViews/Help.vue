<template>
  <v-container>
    <sub-nav subtitle="Help" :items="items" />
    <v-row>
      <v-col class="col-9">
        <p>Things will go here later</p>
      </v-col>
    </v-row>
  </v-container>
</template>

<script lang="ts">
import { defineComponent } from "vue";
import SubNav from "@/components/SubNav.vue";

export default defineComponent({
  name: "HelpView",
  components: {
    SubNav,
  },
  setup() {
    const items = [
      {
        title: "General",
        to: "/dashboard/settings/general",
      },
      {
        title: "Manual",
        to: "/dashboard/settings/profile",
      },
      {
        title: "Community",
        to: "/dashboard/settings/users",
      },
    ];
    return { items };
  },
});
</script>
