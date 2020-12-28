// miragejs server for mocking a backend during development
import { createServer, RestSerializer, Model, Factory, belongsTo, association, trait } from "miragejs"
import faker from "faker"

let ApplicationSerializer = RestSerializer.extend({
  root: false,
  embed: true,
  keyForAttribute(attr) {
    return attr;
  }
})

export function makeServer({ environment = "development" } = {}) {
  let server = createServer({
    environment,

    models: {
      file: Model,
      playouthistory: Model.extend({
        file: belongsTo(),
      }),
    },
    serializers: {
      application: ApplicationSerializer,
      playouthistory: ApplicationSerializer.extend({
        include: ['file'],
      }),
    },
    factories: {
      file: Factory.extend({
        track_title() {
          return faker.commerce.productName();
        },
        artist_name() {
          return faker.name.findName();
        },
      }),
      playouthistory: Factory.extend({
        starts() {
          return faker.date.recent(7);
        },
        ends() {
          // playout entries have a length of up to 6 minutes
          let diff = Math.floor(Math.random() * 6) + 1;
          return new Date(this.starts.getTime() + diff*60000);
        },
        metadata() {
          return {
            showname: faker.commerce.productName(),
          };
        },
        withFile: trait({
          file: association(),
        }),
      }),
    },
    seeds(server) {
      server.create("playouthistory", {
        metadata: {
          showname: faker.commerce.productName(),
          artist_name: faker.name.findName(),
        }
      })
      server.create("playouthistory", "withFile")
    },
    routes() {
      this.namespace = "api/v2";

      this.get("/playout-history", (schema) => {
        return schema.playouthistories.all()
      });
    },
  })

  return server
}
