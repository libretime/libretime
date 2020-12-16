<template>
  <v-app dark>
    <v-app-bar app color="primary">
    </v-app-bar>

    <v-main>
      <v-container>
        <ExportDataButtonMenu :data="[]"/>
      </v-container>
    </v-main>
  </v-app>
</template>

<script>
import ExportDataButtonMenu from './components/ExportDataButtonMenu';

export default {
  name: 'App',

  components: {
    ExportDataButtonMenu,
  },

  data: () => ({
    //
  }),
};
</script>
