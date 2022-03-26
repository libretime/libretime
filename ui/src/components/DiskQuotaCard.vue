<template>
  <v-card>
    <v-progress-linear v-model="state.diskUsePer" color="#ff5d1a" height="25">
    </v-progress-linear>
  </v-card>
</template>

<script lang="ts">
import { defineComponent, reactive } from "vue";

export default defineComponent({
  name: "DiskQuotaCard",
  setup() {
    const state = reactive({ diskUsePer: 50 });
    return {
      state,
    };
  },
});
</script>
