<template>
  <div class="about">
    <v-skeleton-loader type="table" v-if="!initialized"/>
    <vc-date-picker mode="dateTime" v-model="range" is-range is-dark v-if="initialized">
      <template v-slot="{ inputValue, inputEvents }">
        <v-row>
          <v-col cols=12 md=3>
            <h2>{{ $t('Playout History') }}</h2>
          </v-col>
          <v-spacer/>
          <v-col cols=12 md=3>
            <v-text-field dense readonly :label="$t('Start Time')" :value="inputValue.start" v-on="inputEvents.start"/>
          </v-col>
          <v-col cols=12 md=3>
            <v-text-field dense readonly :label="$t('End Time')" :value="inputValue.end" v-on="inputEvents.end"/>
          </v-col>
          <v-col cols=12 md=2>
            <ExportDataButtonMenu data="[]"/>
          </v-col>
        </v-row>
      </template>
    </vc-date-picker>
    <v-data-table dense :headers="headers" :items="items" :loading="loading" v-if="initialized"/>
  </div>
</template>

<script>
import ExportDataButtonMenu from '../components/ExportDataButtonMenu';
import PlayoutHistoryService from '../services/PlayoutHistoryService';

export default {
  name: 'AnalyticsPlayoutHistory',
  components: {
    ExportDataButtonMenu,
  },
  data: () => ({
    initialized: false,
    loading: true,
    range: {
      start: new Date(),
      end: new Date(),
    },
    headers: [
      {
        text: "Start Time",
        value: "starts",
      },
      {
        text: "End Time",
        value: "ends",
      },
      {
        text: "Title",
        value: "file.track_title",
      },
      {
        text: "Creator",
        value: "file.artist_name",
      },
      {
        text: "Show",
        value: "metadata.showname",
      },
      {
        text: "Show Creator",
        value: "metadata.artist_name",
      },
    ],
    items: [],
  }),
  methods: {
    fetch() {
      PlayoutHistoryService.getAll()
        .then(response => {
          this.items = response.data;
          this.loading = false;
        })
        .catch(e => {
          this.items = [];
          this.loading = false;
          this.err = e;
        })
    }
  },
  mounted() {
    this.initialized = true;
    this.fetch();
  },
};
</script>
