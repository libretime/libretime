import axios from 'axios'
// let apiURL = 'http://192.168.64.43:8080'

export default axios.create({
  baseURL: 'localhost:8080' + '/api/v2',
  headers: {
    'Content-type': 'application/json',
    // Authorization: 'Api-Key WIQJQZ1MDSKATO2T53TR',
  },
})
