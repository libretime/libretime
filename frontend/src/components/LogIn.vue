<template>
  <v-card>
    <v-card-title class="text-h5">
      {{ $t("components.login.title") }}
    </v-card-title>
    <v-card-text>
      <v-form>
        <v-text-field
          v-model="email"
          :label="$t('labels.email')"
          type="email"
          required
        />
        <v-text-field
          v-model="password"
          :label="$t('labels.password')"
          type="password"
          required
        />
        <v-btn
          block
          color="primary"
        >
          {{ $t("components.login.title") }}
        </v-btn>
      </v-form>
    </v-card-text>
  </v-card>
</template>

<script setup lang="ts">
import { ref } from 'vue'

const email = ref("")
const password = ref("")
</script>
