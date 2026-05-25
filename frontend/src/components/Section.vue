<template>
  <v-toolbar
    color="primary"
    :title="props.title"
  />
  <div class="d-flex">
    <v-tabs
      v-model="tab"
      color="primary"
      direction="vertical"
    >
      <slot name="tabs" />
    </v-tabs>
    <v-tabs-window v-model="tab">
      <slot name="content" />
    </v-tabs-window>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'

interface SectionProps {
  /** the title of the section */
  title: string,
}

defineSlots<{
  /** The v-tab items to list down the left side */
  tabs: () => void,
  /** The content to display when the tab is selected. Should be a list of v-tabs-window-items */
  content: () => void,
}>()

const props = defineProps<SectionProps>()
const tab = ref("getting-started")
</script>
