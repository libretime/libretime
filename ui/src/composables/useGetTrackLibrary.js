import { ref, reactive, toRefs } from '@vue/composition-api'
import axios from 'axios'
// import dayjs from 'dayjs'
// import { customParseFormat } from 'dayjs/plugin/customParseFormat'

export default function () {
  const tracks = reactive({ list: [] })
  axios
    .get('http://localhost:8888/api/v2/files.json')
    .then((response) => (tracks.list = ref(response.data)))
    .catch((err) => console.log(err))

  console.log(tracks)
  console.log(toRefs(tracks))

  // dayjs.extend(customParseFormat) // may not import correctly, docs unclear
  // tracks.list.forEach(function (x) {
  //   let temp = x.length.split('.', 1)
  //   x.length = dayjs(temp[1], 'H:mm:ss')
  // })
  return toRefs(tracks)
}
