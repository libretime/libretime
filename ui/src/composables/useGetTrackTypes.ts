import { ref, toRefs, reactive } from "vue";
import api from "./useGetAPI";

export default function () {
  const contents = reactive({ list: [] });
  api
    .get("/track-types/")
    .then((res) => (contents.list = ref(res.data)))
    .catch((Error)) => console.log(Error)

  return toRefs(contents)
}
