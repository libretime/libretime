<template>
  <v-form>
    <v-checkbox v-model="enabled" :label="`Enabled`" />
    <v-checkbox v-model="mobile" :label="`Mobile`" />
    <v-text-field v-model="sourceURL" :label="`Source (Required)`" />
    <v-text-field v-model="port" :label="`Port (Required)`" />
  </v-form>
</template>

<script lang="ts">
import { defineComponent } from "vue";

export default defineComponent({
  name: "OutputStreamCard",
});
</script>
