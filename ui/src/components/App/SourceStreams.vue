<template>
  <v-container>
    <v-row>
      <!-- <v-col class="col-2">
        <v-switch class="pa-0 body-2" flat color="info">Master Source</v-switch>
        <v-switch class="pa-0 body-2" flat color="info">Show Source</v-switch>
      </v-col> -->
      <v-col>
        <v-btn block color="error">ON AIR</v-btn>
        <v-btn block x-small>LISTEN</v-btn>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
export default {
  name: 'SourceStreams',
}
</script>

<style lang="sass"></style>