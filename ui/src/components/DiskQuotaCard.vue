<template>
  <v-card>
    <v-progress-linear v-model="disk" color="#ff5d1a" height="25">
      <template #default="{ value }">
        <strong>{{ value }}%</strong>
      </template>
    </v-progress-linear>
  </v-card>
</template>

<script lang="ts">
import { defineComponent } from "vue";

export default defineComponent({
  name: "DiskQuotaCard",
  setup() {
    const value = 75;
    return value;
  },
});
</script>
