import axios from 'axios'

export default axios.create({
  baseURL: '/api/v2',
  headers: {
    'Content-type': 'application/json',
  },
})
