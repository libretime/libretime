<template>
  <div class="text-center">
    <v-dialog v-model="dialog">
      <template v-slot:activator="{ props }">
        <v-btn color="primary" v-bind="props"> Add User </v-btn>
      </template>

      <v-card>
        <v-card-title class="text-h5">Add User</v-card-title>
        <v-form>
          <v-row>
            <v-col>
              <v-text-field
                v-model="userInfo.first_name"
                label="First Name"
              ></v-text-field>
            </v-col>
            <v-col>
              <v-text-field
                v-model="userInfo.last_name"
                label="Last Name"
              ></v-text-field>
            </v-col>
          </v-row>
          <v-text-field v-model="userInfo.email" label="Email"></v-text-field>
          <v-text-field
            v-model="userInfo.username"
            label="Username"
          ></v-text-field>
          <v-row>
            <v-col>
              <v-text-field
                v-model="userInfo.password"
                label="Password"
                :type="'password'"
              ></v-text-field>
            </v-col>
            <v-col>
              <v-text-field
                v-model="userInfo.verifyPassword"
                label="Verify Password"
                :type="'password'"
              ></v-text-field>
            </v-col>
          </v-row>
          <v-overflow-btn
            class="my-2"
            :items="userTypes"
            label="User Type"
          ></v-overflow-btn>
        </v-form>
        <v-divider></v-divider>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="orange darken-3" text @click="state.dialog = false"
            >Submit</v-btn
          >
          <v-btn color="gray" text @click="state.dialog = false">{{
            $t("message.cancel")
          }}</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </div>
</template>

<script lang="ts">
import { defineComponent, reactive } from "vue";

export default defineComponent({
  name: "AddUserDialog",
  setup() {
    const state = reactive({ dialog: false });
    return {
      state,
    };
  },
});
</script>
