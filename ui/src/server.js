// miragejs server for mocking a backend during development
import { createServer, Model } from "miragejs"

export function makeServer({ environment = "development" } = {}) {
  let server = createServer({
    environment,

    models: {
      playouthistory: Model,
    },

    routes() {
      this.namespace = "api/v2"

      this.get("/playout-history", () => {
        return [
          {
            "id": 1,
            "starts": new Date(),
            "ends": new Date(),
            "file": {
              "track_title": "Snake Oil Blues",
              "artist_name": "Hairmare's Traveling Medicine Show",
            },
            "metadata": {
              "showname": "Untiiled Show",
            }
          }
        ]
      })
    },
  })

  return server
}
