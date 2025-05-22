<template>
  <v-card>
    <v-toolbar
      color="primary"
      title="Help"
    />

    <div class="d-flex flex-row">
      <v-tabs
        v-model="tab"
        color="primary"
        direction="vertical"
      >
        <v-tab
          :text="$t('components.help.gettingStarted.tab')"
          value="getting-started"
        />
        <v-tab
          :text="$t('components.help.userManual.tab')"
          value="getting-started"
          href="https://libretime.org/docs"
        />
        <v-tab
          :text="$t('components.help.getHelpOnline.tab')"
          value="getting-started"
          href="https://discourse.libretime.org/"
        />
        <v-tab
          :text="$t('components.help.contribute.tab')"
          value="getting-started"
          href="https://libretime.org/docs/contribute"
        />
        <v-tab
          :text="$t('components.help.whatsNew.tab')"
          value="getting-started"
          href="https://github.com/libretime/libretime/releases"
        />
      </v-tabs>
      <v-tabs-window v-model="tab">
        <!-- the only valid tab is getting started, the rest are hyperlinks -->
        <v-tabs-window-item value="getting-started">
          <Info>
            <h1>
              {{ $t('components.help.gettingStarted.title') }}
            </h1>
            <p>{{ $t('components.help.gettingStarted.intro') }}</p>
            <ol class="list-spaced">
              <li>
                <p>
                  <b>{{ $t('components.help.gettingStarted.step1.title') }}</b>
                </p>
                <p>
                  {{ $t('components.help.gettingStarted.step1.description') }}
                </p>
              </li>
              <li>
                <p>
                  <b>{{ $t('components.help.gettingStarted.step2.title') }}</b>
                </p>
                <p>
                  {{ $t('components.help.gettingStarted.step2.description') }}
                </p>
              </li>
              <li>
                <p>
                  <b>{{ $t('components.help.gettingStarted.step3.title') }}</b>
                </p>
                <p>
                  {{ $t('components.help.gettingStarted.step3.description') }}
                </p>
              </li>
            </ol>
            <p><b>{{ $t('components.help.gettingStarted.conclusion') }}</b></p>
            <p>{{ $t('components.help.gettingStarted.moreInfo') }} </p>
          </Info>
        </v-tabs-window-item>
      </v-tabs-window>
    </div>
  </v-card>
</template>

<script setup lang="ts">
import { ref } from 'vue'

const tab = ref("getting-started")
</script>

<style scoped lang="scss">
.list-spaced {
  margin-left: 25px;
  margin-top: 1em;
}

.list-spaced > li {
  margin-bottom: 1em;
}
</style>
