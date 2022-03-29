<template>
  <v-container>
    <v-row>
      <v-col>
        <p class="text-h6">My Profile</p>
      </v-col>
      <v-spacer></v-spacer>
      <v-col>
        <v-btn color="grey" plain> Save </v-btn>
      </v-col>
    </v-row>
    <v-row>
      <v-col>
        <v-form>
          <v-text-field v-model="me.username" :label="`Username`" disabled />
          <v-text-field
            v-model="me.password"
            :type="`password`"
            :label="`Password`"
          />
          <v-text-field
            v-model="me.verifyPassword"
            :type="`password`"
            :label="`Verify Password`"
          />
          <v-text-field v-model="me.firstName" :label="`First Name`" />
          <v-text-field v-model="me.lastName" :label="`Last Name`" />
          <v-text-field v-model="me.email" :label="`Email`" />
          <v-text-field v-model="me.phone" :label="`Phone Number`" />
        </v-form>
      </v-col>
    </v-row>
  </v-container>
</template>

<script lang="ts">
import { defineComponent, reactive } from "vue";

export default defineComponent({
  name: "MyProfileView",
  setup() {
    const me = reactive({
      username: "User",
      password: "Password",
      verifyPassword: "Password",
      firstName: "Joe",
      lastName: "Wise",
      email: "you@me.com",
      phone: "867-5309",
    });
    return { me };
  },
});
</script>
