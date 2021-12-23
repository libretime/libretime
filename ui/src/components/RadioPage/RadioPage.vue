<template>
  <v-container fluid>
    <v-parallax height="800" src="https://libretime.org/img/radio-unsplash.jpg">
      <v-row justify="center" align="center">
        <v-card class="mx-auto" max-width="344">
          <v-card-text>
            <p class="text-h4 station-name">Libretime FM</p>
            <v-btn color="grey" plain @click="gotoSite">libretime.org</v-btn>
            <div class="text--primary">Built by a passionate team of volunteers</div>
          </v-card-text>
          <now-playing-widget />
          <v-card-actions>
            <v-btn text color="#ff5d1a" @click="reveal = true"> {{ $t('message.login') }} </v-btn>
          </v-card-actions>
          <v-expand-transition>
            <v-card
              v-if="reveal"
              class="transition-fast-in-fast-out v-card--reveal"
              style="height: 100%"
            >
              <v-card-text class="pb-0">
                <p class="text-h4 text--primary">{{ $t('message.login') }}</p>
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
              <v-card-actions class="pt-0">
                <v-btn text color="#ff5d1a" @click="tryLogin"> {{ $t('message.login') }} </v-btn>
                <v-btn text color="grey accent-4" @click="reveal = false">
                  {{ $t('message.close') }}
                </v-btn>
              </v-card-actions>
            </v-card>
          </v-expand-transition>
        </v-card>
      </v-row>
    </v-parallax>
  </v-container>
</template>

<script>
import NowPlayingWidget from '../Widgets/NowPlayingWidget.vue'
export default {
  name: 'RadioPage',
  components: { NowPlayingWidget },
  data: () => {
    return {
      reveal: false,
      username: null,
      password: null,
    }
  },
  methods: {
    tryLogin() {
      this.$emit('try-login', this.username)
      this.reveal = false
      return
    },
    gotoSite() {
      window.open('https://libretime.org')
    },
  },
}
</script>

<style lang="sass">
@use '../../assets/styles/base'

// body
//   background: radial-gradient(base.$background, base.$libretime-orange)

.station-name
  color: base.$libretime-orange
</style>