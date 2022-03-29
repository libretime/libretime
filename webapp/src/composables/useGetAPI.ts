import { reactive } from "vue";
import axios from "axios";

export default function (path: string) {
  const contents = reactive({ list: [] });
  axios
    .get("/api/v2/" + path, {
      headers: {
        "Content-type": "application/json",
      },
    })
    .then((res) => (contents.list = res.data))
    .catch((err) => console.log(err));
  return {
    contents,
  };
}
