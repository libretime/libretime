<template>
  <v-container>
    <v-dialog v-model="dialog" width="500" persistent>
      <template #activator="{ on, attrs }">
        <v-btn color="orange darken-3" dark v-bind="attrs" v-on="on">Add</v-btn>
      </template>
      <v-card>
        <v-card-title class="text-h5">Add User</v-card-title>
        <v-form>
          <v-row>
            <v-col>
              <v-text-field v-model="userInfo.first_name" label="First Name"></v-text-field>
            </v-col>
            <v-col>
              <v-text-field v-model="userInfo.last_name" label="Last Name"></v-text-field>
            </v-col>
          </v-row>
          <v-text-field v-model="userInfo.email" label="Email"></v-text-field>
          <v-text-field v-model="userInfo.username" label="Username"></v-text-field>
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
          <v-overflow-btn class="my-2" :items="userTypes" label="User Type"></v-overflow-btn>
        </v-form>
        <v-divider></v-divider>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="orange darken-3" text @click="submitForm()">Submit</v-btn>
          <v-btn color="gray" text @click="dialog = false">{{ $t('message.cancel') }}</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </v-container>
</template>

<script>
export default {
  name: 'AddUser',
  data() {
    return {
      userInfo: {
        username: null,
        password: null,
        verifyPassword: null,
        first_name: null,
        last_name: null,
        email: null,
        type: null,
      },
      userTypes: ['Admin', 'Program Manager', 'DJ', 'Guest'],
      dialog: false,
    }
  },
  methods: {
    submitForm: () => {
      // Check to see if passwords match
      if (this.userInfo.password === this.userInfo.verifyPassword) {
        // Continue
      }
      // Close form at the end
      this.dialog = false
    },
  },
}
</script>