<template>
  <div class="about">
    <ExportDataButtonMenu :data="[]"/>
  </div>
</template>

<script>
import ExportDataButtonMenu from '../components/ExportDataButtonMenu';

export default {
  name: 'AnalyticsPlayoutHistory',

  components: {
    ExportDataButtonMenu,
  },

  data: () => ({
    //
  }),
};
</script>
