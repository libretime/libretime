import { ref, reactive, toRefs } from '@vue/composition-api'
import axios from 'axios'
// import dayjs from 'dayjs'
// import { customParseFormat } from 'dayjs/plugin/customParseFormat'

export default function () {
  const contents = reactive({ list: [] })
  axios
    .get('http://localhost:8888/api/v2/...')
    .then((response) => (contents.list = ref(response.data)))
    .catch((err) => console.log(err))

  console.log(contents)
  console.log(toRefs(contents))

  // dayjs.extend(customParseFormat) // may not import correctly, docs unclear
  // contents.list.forEach(function (x) {
  //   let temp = x.length.split('.', 1)
  //   x.length = dayjs(temp[1], 'H:mm:ss')
  // })
  return toRefs(contents)
}
