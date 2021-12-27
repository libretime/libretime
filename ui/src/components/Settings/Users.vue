<template>
  <v-container>
    <p class="text-h5">Users</p>
    <v-toolbar dense><add-user /></v-toolbar>
    <v-data-table
      :headers="headers"
      :items="userList"
      :items-per-page="10"
      class="elevation-1"
    ></v-data-table>
  </v-container>
</template>

<script>
import AddUser from '../../components/Settings/AddUser.vue'
// import api from '../../api'
const apidata = require('../../../public/api/v2/users.json')

export default {
  name: 'Users',
  components: { AddUser },
  data() {
    return {
      headers: [
        { text: 'Username', value: 'username' },
        { text: 'First Name', value: 'first_name' },
        { text: 'Last Name', value: 'last_name' },
        { text: 'Email', value: 'email' },
        { text: 'Role', value: 'fullType' },
      ],
      userList: apidata,
    }
  },
  mounted: () => {
    // Get list of users
    // api
    //   .get('/users/')
    //   .then((response) => (this.userList = response.data))
    //   .catch((error) => console.log(error))

    // Replace role abbreviations with full names (i.e. A -> Administrator)
    this.userList.forEach((element) => {
      switch (element.type) {
        case 'A':
          element.fullType = 'Administrator'
          break
        case 'P':
          element.fullType = 'Program Director'
          break
        case 'D':
          element.fullType = 'DJ'
          break
        case 'G':
          element.fullType = 'Guest'
          break
      }
    })
  },
}
</script>