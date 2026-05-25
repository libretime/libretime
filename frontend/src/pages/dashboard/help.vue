<template>
  <v-card class="fill-height">
    <Section
      :title="$t('pages.help.title')"
    >
      <template #tabs>
        <v-tab
          :text="$t('pages.help.gettingStarted.tab')"
          value="getting-started"
        />
        <v-tab
          :text="$t('pages.help.userManual.tab')"
          value="getting-started"
          href="https://libretime.org/docs"
        />
        <v-tab
          :text="$t('pages.help.getHelpOnline.tab')"
          value="getting-started"
          href="https://discourse.libretime.org/"
        />
        <v-tab
          :text="$t('pages.help.contribute.tab')"
          value="getting-started"
          href="https://libretime.org/docs/contribute"
        />
        <v-tab
          :text="$t('pages.help.whatsNew.tab')"
          value="getting-started"
          href="https://github.com/libretime/libretime/releases"
        />
      </template>
      <template #content>
        <!-- the only valid tab is getting started, the rest are hyperlinks -->
        <v-tabs-window-item value="getting-started">
          <Info>
            <h1>
              {{ $t('pages.help.gettingStarted.title') }}
            </h1>
            <p>{{ $t('pages.help.gettingStarted.intro') }}</p>
            <ol class="list-spaced">
              <li>
                <p>
                  <b>{{ $t('pages.help.gettingStarted.step1.title') }}</b>
                </p>
                <p>
                  {{ $t('pages.help.gettingStarted.step1.description') }}
                </p>
              </li>
              <li>
                <p>
                  <b>{{ $t('pages.help.gettingStarted.step2.title') }}</b>
                </p>
                <p>
                  {{ $t('pages.help.gettingStarted.step2.description') }}
                </p>
              </li>
              <li>
                <p>
                  <b>{{ $t('pages.help.gettingStarted.step3.title') }}</b>
                </p>
                <p>
                  {{ $t('pages.help.gettingStarted.step3.description') }}
                </p>
              </li>
            </ol>
            <p><b>{{ $t('pages.help.gettingStarted.conclusion') }}</b></p>
            <p>{{ $t('pages.help.gettingStarted.moreInfo') }} </p>
          </Info>
        </v-tabs-window-item>
      </template>
    </Section>
  </v-card>
</template>

<script setup lang="ts">
import Section from "@/components/Section.vue"
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
