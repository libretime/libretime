<template>
  <v-app dark>
    <v-app-bar app color="primary">
      <router-link to="/">Home</router-link> |
      <router-link to="/playouthistory">Analytics » Playout History</router-link>
    </v-app-bar>

    <v-main>
      <v-container>
        <router-view/>
      </v-container>
    </v-main>
  </v-app>
</template>
