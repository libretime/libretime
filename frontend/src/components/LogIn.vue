<template>
  <v-card>
    <v-card-title class="text-h5">
      {{ $t("components.login.title") }}
    </v-card-title>
    <v-card-text>
      <v-form>
        <v-text-field
          v-model="email"
          :label="$t('components.login.email')"
          type="email"
          required
        />
        <v-text-field
          v-model="password"
          :label="$t('components.login.password')"
          type="password"
          required
        />
        <v-btn
          block
          color="primary"
          @click="login"
        >
          {{ $t("components.login.title") }}
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
