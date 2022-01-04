<template>
  <v-container>
    <p class="text-h5">General Settings</p>
    <v-form>
      <v-container>
        <v-row>
          <v-col>
            <v-text-field v-model="stationName" label="Station Name"></v-text-field>
            <v-textarea v-model="stationDesc" label="Station Description"></v-textarea>
            <v-file-input show-size counter multiple label="Station Logo"></v-file-input>
            <p class="text-subtitle-2">Note: logos larger than 600x600 will be resized.</p>
            <v-divider />
            <v-select :items="lang" label="Default Language" />
            <v-select :items="timezone" label="Station Timezone" />
            <v-select :items="days" label="Week Starts On" />
            <v-text-field v-model="fadeIn" label="Default Fade In (sec)" />
            <v-text-field v-model="fadeOut" label="Default Fade Out (sec)" />
            <v-text-field v-model="fadeCross" label="Default Crossfade Duration (sec)" />
            <v-select :items="playlists" label="Intro Autoloading Playlist" />
            <v-select :items="playlists" label="Outro Autoloading Playlist" />
            <v-checkbox v-model="podcastMetatags" :label="`Overwrite Podcast Episode Metatags`" />
            <v-checkbox
              v-model="newPodcastGen"
              :label="`Generate a smartblock and a playlist upon creation of a new podcast`"
            />
            <v-checkbox v-model="featurePreviews" :label="`Display feature previews?`" />
            <v-checkbox v-model="publicAPI" :label="`Enable Libretime's Public API`" />
            <v-textarea v-model="cors" label="Allowed CORS URLs" />
            <v-checkbox
              v-model="displayLoginBtn"
              :label="`Display login button on your Radio Page?`"
            />
            <v-divider />
            <v-expansion-panels>
              <v-expansion-panel>
                <v-expansion-panel-header> Dangerous Options </v-expansion-panel-header>
                <v-expansion-panel-content>
                  <v-btn elevation="2" color="error" @click="deleteLibrary()"
                    >Delete All Tracks in Library</v-btn
                  >
                </v-expansion-panel-content>
              </v-expansion-panel>
            </v-expansion-panels>
          </v-col>
        </v-row>
      </v-container>
    </v-form>
  </v-container>
</template>

<script>
export default {
  name: 'GeneralSettings',
  data() {
    return {
      stationName: '',
      stationDesc: '',
      lang: ['English', 'Spanish'],
      timezone: ['UTC', '+5'],
      days: ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'],
      fadeIn: '',
      fadeOut: '',
      fadeCross: '',
      playlists: ['Fake playlist 1', 'Fake playlist 2'],
      podcastMetatags: true,
      newPodcastGen: true,
      featurePreviews: true,
      publicAPI: true,
      cors: '',
      displayLoginBtn: true,
    }
  },
  methods: {
    deleteLibrary: () => {
      return
    },
  },
}
</script>