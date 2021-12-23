<template>
  <v-card>
    <v-progress-linear v-model="disk" color="#ff5d1a" height="25">
      <template #default="{ value }">
        <strong>{{ value }}%</strong>
      </template>
    </v-progress-linear>
  </v-card>
</template>

<script>
export default {
  name: 'DiskQuotaSm',
  data() {
    return {
      disk: '78',
    }
  },
}
</script>

<style></style>