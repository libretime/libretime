<template>
  <v-card class="mx-auto" max-width="300">
    <v-card-text>
      <v-card-title>/_a</v-card-title>
      <v-card-subtitle>Stream 1</v-card-subtitle>
      <v-form>
        <v-select v-model="service" :items="selectItems.service" label="Service" />
        <v-select v-model="codec" :items="selectItems.codec" label="Stream Codec" />
        <v-select v-model="bitRate" :items="selectItems.bitRate" label="Bit Rate" />
        <v-text-field v-model="server" label="Server" />
        <v-text-field v-model="port" label="Port" />
        <v-checkbox v-model="mono" label="Mono Stream" />
        <div>Stream URL</div>
      </v-form>
    </v-card-text>
    <v-card-actions>
      <v-btn text color="deep-purple accent-4"> Enable </v-btn>
      <v-btn text color="deep-purple accent-4"> Save </v-btn>
    </v-card-actions>
  </v-card>
</template>

<script>
export default {
  name: 'StreamCard',
  data() {
    return {
      enabled: true,
      service: 'Icecast',
      codec: 'Ogg Vorbis',
      bitRate: '128 kbps',
      server: '',
      port: '',
      mono: false,
      selectItems: {
        service: ['Icecast', 'Shoutcast'],
        codec: ['Ogg Vorbis', 'MP3', 'AAC'],
        bitRate: ['96 kbps', '128 kbps', '160 kbps', '192 kbps', '256 kbps', '320 kbps'],
      },
    }
  },
}
</script>

<style lang="sass"></style>