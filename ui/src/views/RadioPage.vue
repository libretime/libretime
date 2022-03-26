<template>
  <v-container fluid>
    <v-parallax height="800" src="https://libretime.org/img/radio-unsplash.jpg">
      <v-row justify="center" align="center">
        <login-form />
        <v-card class="mx-auto" max-width="344">
          <v-card-text>
            <p class="text-h4 station-name">Libretime FM</p>
            <v-btn color="grey" plain>libretime.org</v-btn>
            <div class="text--primary">
              Built by a passionate team of volunteers
            </div>
          </v-card-text>
        </v-card>
      </v-row>
    </v-parallax>
  </v-container>
</template>

<script lang="ts">
import { defineComponent } from "vue";
import LoginForm from "@/components/RadioPage/LoginForm.vue";

export default defineComponent({
  name: "RadioPage",
  components: {
    LoginForm,
  },
});
</script>
