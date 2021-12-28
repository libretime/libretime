<template>
  <div>
    <v-row>
      <p class="text-h6">Manage Track Types</p>
      <v-col>
        <v-toolbar dense>
          <v-btn color="primary" @click="resetForm">New Track Type</v-btn>
        </v-toolbar>
        <v-text-field
          v-model="search"
          :label="$t('message.search')"
          filled
          single-line
          hide-details
          append-icon="mdi-magnify"
        />
        <v-data-table
          :headers="headers"
          :items="typesList"
          :search="search"
          :items-per-page="10"
          class="elevation-1"
        />
      </v-col>
      <v-col>
        <v-form v-show="showEditor" ref="TrackTypesForm" lazy-validation>
          <v-text-field v-model="type.name" label="Type Name" />
          <v-text-field
            v-model="type.code"
            :rules="[(v) => !!v || 'Code is required']"
            label="Code"
          />
          <v-textarea v-model="type.description" label="Description" />
          <v-checkbox v-model="type.enabled" label="Visiblity" />
          <v-btn color="primary" class="mr-4" @click="submit">Save</v-btn>
        </v-form>
      </v-col>
    </v-row>
  </div>
</template>

<script>
export default {
  name: 'TrackTypes',
  data() {
    return {
      type: {
        name: '',
        code: '',
        description: '',
        enabled: true,
      },
      headers: [
        { text: 'Code', value: 'code' },
        { text: 'Label', value: 'label' },
        { text: 'Description', value: 'description' },
        { text: 'Visibility', value: 'visibility' },
      ],
      showEditor: false,
      search: '',
      typesList: [],
    }
  },
  methods: {
    resetForm: () => {
      this.showEditor = true
      this.$refs.TrackTypesForm.reset()
    },
    submit: () => {
      this.$refs.TrackTypesForm.validate()
    },
  },
}
</script>
