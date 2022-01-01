<template>
  <div class="text-center">
    <v-dialog v-model="dialog" width="600" persistent>
      <template #activator="{ on, attrs }">
        <v-list-item v-bind="attrs" link v-on="on">
          <v-list-item-icon>
            <v-icon>mdi-upload</v-icon>
          </v-list-item-icon>
          <v-list-item-title>Upload</v-list-item-title>
        </v-list-item>
      </template>
      <v-card>
        <v-card-title class="text-h5">{{ $t('message.uploadShows') }}</v-card-title>
        <v-file-input
          v-model="files"
          color="deep-orange accent-4"
          counter
          multiple
          placeholder="Select your files"
          prepend-icon="mdi-paperclip"
          outlined
          :show-size="1000"
          @change="selectFile"
        >
          <template #selection="{ index, text }">
            <v-chip v-if="index < 2" color="deep-purple accent-4" dark label small>
              {{ text }}
            </v-chip>
            <span v-else-if="index === 2" class="text-overline grey--text text--darken-3 mx-2">
              +{{ files.length - 2 }} File(s)
            </span>
          </template>
        </v-file-input>
        <v-divider></v-divider>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="primary" text @click="dialog = false">{{ $t('message.upload') }}</v-btn>
          <v-btn color="gray" text @click="closeDialog()">{{ $t('message.cancel') }}</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </div>
</template>

<script>
export default {
  name: 'UploadShows',
  data() {
    return {
      dialog: false,
      progress: 0,
      files: [],
    }
  },
  methods: {
    closeDialog: function () {
      this.dialog = false
      this.files = []
      return
    },
    selectFile(file) {
      this.progress = 0
      this.files = file
    },
  },
}
</script>
