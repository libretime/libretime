<template>
  <v-container fluid>
    <v-sheet class="pa-5" color="grey lighten-3">
      <v-row no-gutters>
        <v-col></v-col>
        <!-- For empty space -->
        <v-spacer />
        <v-col md="1">
          <login-form />
        </v-col>
      </v-row>
      <div></div>
      <v-sheet
        :elevation="6"
        :rounded="true"
        class="mx-auto"
        height="350"
        width="350"
      >
        <v-card class="mx-auto">
          <v-card-text>
            <p class="text-h4 station-name">Libretime FM</p>
            <v-btn color="grey" plain>libretime.org</v-btn>
            <div class="text--primary">
              Built by a passionate team of volunteers
            </div>
          </v-card-text>
        </v-card>
      </v-sheet>
      <radio-player />
    </v-sheet>
  </v-container>
</template>

<script lang="ts">
import { defineComponent } from "vue";
import LoginForm from "@/components/RadioPage/LoginForm.vue";
import RadioPlayer from "@/components/RadioPage/RadioPlayer.vue";

export default defineComponent({
  name: "RadioPage",
  components: {
    LoginForm,
    RadioPlayer,
  },
});
</script>
