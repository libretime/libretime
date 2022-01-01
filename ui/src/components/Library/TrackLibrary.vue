<template>
  <v-container>
    <v-card>
      <v-tabs centered show-arrows>
        <v-tab>Tracks</v-tab>
        <v-tab>Playlists</v-tab>
        <v-tab>Smart Blocks</v-tab>
        <v-tab>Webstreams</v-tab>
        <v-tab>Podcasts</v-tab>
      </v-tabs>
      <v-text-field
        v-model="search"
        :label="$t('message.search')"
        filled
        single-line
        hide-details
        append-icon="mdi-magnify"
      ></v-text-field>
      <v-toolbar dense>
        <v-btn color="primary"><v-icon>mdi-plus-thick</v-icon>New</v-btn>
        <v-btn><v-icon>mdi-pencil</v-icon>Edit</v-btn>
        <v-btn><v-icon>mdi-plus-thick</v-icon>Add to current show</v-btn>
        <v-btn color="error"><v-icon>mdi-delete</v-icon>Delete</v-btn>
        <v-spacer></v-spacer>
        <v-btn>Columns<v-icon>mdi-chevron-down</v-icon></v-btn>
      </v-toolbar>
      <div>
        <v-data-table
          :headers="headers"
          :items="files"
          :search="search"
          :items-per-page="10"
          class="elevation-1"
        ></v-data-table>
      </div>
    </v-card>
  </v-container>
</template>

<script>
import useGetTrackLibrary from '@/composables/useGetTrackLibrary.js'
export default {
  setup() {
    const { files } = useGetTrackLibrary()
    console.log(files)
    return { files }
  },
  data() {
    return {
      search: '',
      headers: [
        { text: 'Title', value: 'track_title' },
        { text: 'Artist', value: 'artist_name' },
        { text: 'Album', value: 'album_title' },
        { text: 'Genre', value: 'genre' },
        { text: 'Length', value: 'length' },
      ],
    }
  },
}

// import dayjs from 'dayjs'
// import { customParseFormat } from 'dayjs/plugin/customParseFormat'
// import axios from 'axios'

// export default {
//   name: 'TrackLibrary',
//   data() {
//     return {
//       loading: true,
//       errored: false,
//       search: '',
//       headers: [
//         { text: 'Title', value: 'track_title' },
//         { text: 'Artist', value: 'artist_name' },
//         { text: 'Album', value: 'album_title' },
//         { text: 'Genre', value: 'genre' },
//         { text: 'Length', value: 'length' },
//       ],
//       files: [],
//     }
//   },
//   mounted: function () {
//     axios
//       .get('http://localhost:8888/api/v2/files.json')
//       .then((response) => {
//         this.files = response.data
//         dayjs.extend(customParseFormat) // may not import correctly, docs unclear
//         this.files.forEach(function (x) {
//           let temp = x.length.split('.', 1)
//           x.length = dayjs(temp[1], 'H:mm:ss')
//         })
//       })
//       .catch((err) => {
//         console.log(err)
//         this.errored = true
//       })
//       .finally(() => (this.loading = false))
//   },
// }
</script>