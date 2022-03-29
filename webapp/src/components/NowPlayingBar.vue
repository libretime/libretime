<template>
  <v-app-bar app clipped-left height="125">
    <v-row class="app-bar">
      <v-col class="col-1">
        <v-container>
          <v-img
            class="align-sm-center child-flex"
            src="../../assets/logo.png"
            max-height="100"
            max-width="100"
            contain
          />
        </v-container>
      </v-col>
      <v-col class="col-4">
        <div>
          <p class="body-2 ma-1"><b>Previous:</b> {{ npPrev }}</p>
          <v-card class="mx-0" outlined>
            <v-row>
              <v-col>
                <v-list-item-title class="body-1 pa-1 ps-2">{{
                  npInfo
                }}</v-list-item-title>
              </v-col>
              <v-col class="pe-0 my-1">
                <v-list-item-subtitle class="body-1"
                  ><strong>1:23</strong> 4:00
                </v-list-item-subtitle>
              </v-col>
            </v-row>
            <v-progress-linear
              value="35"
              color="orange darken-2"
            ></v-progress-linear>
          </v-card>
          <p class="body-2 ma-1"><b>Next:</b> {{ npNext }}</p>
        </div>
      </v-col>
      <v-col class="col-4">
        <div>
          <p class="body-2 ma-1 invis">___</p>
          <v-card class="mx-0" outlined>
            <v-row>
              <v-col>
                <v-list-item-title class="body-1 pa-1 ps-2">{{
                  show
                }}</v-list-item-title>
              </v-col>
              <v-col class="pe-0 my-1">
                <v-list-item-subtitle class="body-1">
                  {{ startTime }} - {{ endTime }}
                </v-list-item-subtitle>
              </v-col>
            </v-row>
            <v-progress-linear
              value="15"
              color="blue darken-2"
            ></v-progress-linear>
          </v-card>
          <p class="body-2 ma-1 invis">___</p>
        </div>
      </v-col>
      <v-col>
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
      </v-col>
      <v-col>
        <div class="px-2">
          <p class="text-h3 ma-0">{{ currentTime }}</p>
          <div class="body-2 ma-0">
            <p>Username | Logout</p>
          </div>
        </div>
      </v-col>
    </v-row>
  </v-app-bar>
</template>

<script lang="ts">
import { defineComponent } from "vue";

export default defineComponent({
  name: "NowPlayingBar",
});
</script>

<style scoped>
.app-bar {
  background: rgb(55, 55, 55);
  background: linear-gradient(
    0deg,
    rgba(55, 55, 55, 1) 0%,
    rgba(99, 99, 99, 1) 100%
  );
}
</style>
