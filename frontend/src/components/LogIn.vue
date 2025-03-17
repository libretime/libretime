<template>
  <v-card>
    <v-card-title class="text-h5">
      Log In
    </v-card-title>
    <v-card-text>
      <v-form>
        <v-text-field
          v-model="email"
          label="Email"
          type="email"
          required
        />
        <v-text-field
          v-model="password"
          label="Password"
          type="password"
          required
        />
        <v-btn
          block
          color="primary"
          @click="login"
        >
          Log In
        </v-btn>
      </v-form>
    </v-card-text>
  </v-card>
</template>

<script setup lang="ts">
//
</script>

<script lang="ts">
export default {
  name: "LogIn",
  props: {},
}
</script>
