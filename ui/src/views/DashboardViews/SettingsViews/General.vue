<template>
  <v-container>
    <p class="text-h5">General Settings</p>
    <v-form>
      <v-container>
        <v-row>
          <v-col>
            <v-text-field
              v-model="options.stationName"
              label="Station Name"
            ></v-text-field>
            <v-textarea
              v-model="options.stationDesc"
              label="Station Description"
            ></v-textarea>
            <v-file-input
              show-size
              counter
              multiple
              label="Station Logo"
            ></v-file-input>
            <p class="text-subtitle-2">
              Note: logos larger than 600x600 will be resized.
            </p>
            <v-divider />
            <v-select :items="selectopts.lang" label="Default Language" />
            <v-select :items="selectopts.timezone" label="Station Timezone" />
            <v-select :items="selectopts.days" label="Week Starts On" />
            <v-text-field
              v-model="options.fadeIn"
              label="Default Fade In (sec)"
            />
            <v-text-field
              v-model="options.fadeOut"
              label="Default Fade Out (sec)"
            />
            <v-text-field
              v-model="options.fadeCross"
              label="Default Crossfade Duration (sec)"
            />
            <v-select :items="playlists" label="Intro Autoloading Playlist" />
            <v-select :items="playlists" label="Outro Autoloading Playlist" />
            <v-checkbox
              v-model="options.podcastMetatags"
              :label="`Overwrite Podcast Episode Metatags`"
            />
            <v-checkbox
              v-model="options.newPodcastGen"
              :label="`Generate a smartblock and a playlist upon creation of a new podcast`"
            />
            <v-checkbox
              v-model="options.featurePreviews"
              :label="`Display feature previews?`"
            />
            <v-checkbox
              v-model="options.publicAPI"
              :label="`Enable Libretime's Public API`"
            />
            <v-textarea v-model="options.cors" label="Allowed CORS URLs" />
            <v-checkbox
              v-model="options.displayLoginBtn"
              :label="`Display login button on your Radio Page?`"
            />
            <v-divider />
            <v-expansion-panels>
              <v-expansion-panel>
                <v-expansion-panel-header>
                  Dangerous Options
                </v-expansion-panel-header>
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

<script lang="ts">
import { defineComponent, reactive } from "vue";

export default defineComponent({
  name: "GeneralSettingsView",
  setup() {
    const selectopts = {
      lang: ["English", "Spanish"],
      timezone: ["UTC", "+5"],
      days: [
        "Monday",
        "Tuesday",
        "Wednesday",
        "Thursday",
        "Friday",
        "Saturday",
        "Sunday",
      ],
    };
    const options = reactive({
      stationName: "Libretime",
      stationDesc: "Description",
      fadeIn: "0.5",
      fadeOut: "0.5",
      fadeCross: "0.2",
      podcastMetatags: false,
      newPodcastGen: true,
      featurePreviews: false,
      publicAPI: true,
      coors: "",
      displayLoginBtn: true,
    });
    return { options, selectopts };
  },
});
</script>
