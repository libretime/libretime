<template>
  <div class="text-center">
    <v-dialog v-model="dialog">
      <template v-slot:activator="{ props }">
        <v-btn color="primary" v-bind="props"> Login </v-btn>
      </template>

      <v-card>
        <v-card-text>
          Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do
          eiusmod tempor incididunt ut labore et dolore magna aliqua.
        </v-card-text>
        <v-form>
          <v-container>
            <v-row>
              <v-text-field v-model="username" label="Username"></v-text-field>
              <v-text-field
                v-model="password"
                label="Password"
                type="password"
              ></v-text-field>
            </v-row>
          </v-container>
        </v-form>
        <v-card-actions>
          <v-btn color="primary" block @click="dialog = false">Login</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </div>
</template>

<script lang="ts">
import { defineComponent } from "vue";

export default defineComponent({
  name: "LoginForm",
  data() {
    return {
      dialog: false,
      password: "",
      username: "",
    };
  },
});
</script>
