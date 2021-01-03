// miragejs server for mocking a backend during development
import {
  createServer,
  RestSerializer,
  Model,
  Factory,
  belongsTo,
  hasMany,
  association,
  trait,
} from 'miragejs'
import faker from 'faker'

let ApplicationSerializer = RestSerializer.extend({
  root: false,
  embed: true,
  keyForAttribute(attr) {
    return attr
  },
})

export function makeServer({ environment = 'development' } = {}) {
  let server = createServer({
    environment,

    models: {
      file: Model,
      playouthistory: Model.extend({
        file: belongsTo(),
      }),
      playouthistorytemplate: Model.extend({
        fields: hasMany('playouthistorytemplatefield'),
      }),
      playouthistorytemplatefield: Model.extend({
        template: belongsTo('playouthistorytemplate'),
      }),
    },
    serializers: {
      application: ApplicationSerializer,
      playouthistory: ApplicationSerializer.extend({
        include: ['file'],
      }),
      playouthistorytemplate: ApplicationSerializer.extend({
        include: ['fields'],
      }),
    },
    factories: {
      file: Factory.extend({
        track_title() {
          return faker.commerce.productName()
        },
        artist_name() {
          return faker.name.findName()
        },
      }),
      playouthistory: Factory.extend({
        starts() {
          return faker.date.recent(7)
        },
        ends() {
          // playout entries have a length of up to 6 minutes
          let diff = Math.floor(Math.random() * 6) + 1
          return new Date(this.starts.getTime() + diff * 60000)
        },
        metadata() {
          return {
            showname: faker.commerce.productName(),
          }
        },
        withFile: trait({
          file: association(),
        }),
      }),
      playouthistorytemplate: Factory.extend({
        name(i) {
          return `Template ${i}`
        },
      }),
      playouthistorytemplatefield: Factory.extend({}),
    },
    // the base set of seeds defined below is used when developing against mirage and by storybook
    // in tests mirage needs to be seeded individualy alternatively you can also run the vue dev
    // server using a proxy to the backend.
    seeds(server) {
      server.create('playouthistory', {
        metadata: {
          showname: faker.commerce.productName(),
          artist_name: faker.name.findName(),
        },
      })
      server.create('playouthistory', 'withFile')
      let itemTemplate = server.create('playouthistorytemplate', {
        type: 'item',
      })
      itemTemplate.createField({
        name: 'starts',
        label: 'Start Time',
        position: 1,
      })
      itemTemplate.createField({
        name: 'ends',
        label: 'End Time',
        position: 2,
      })
      itemTemplate.createField({
        name: 'track_title',
        label: 'Title',
        is_file_md: true,
        position: 3,
      })
      itemTemplate.createField({
        name: 'artist_name',
        label: 'Creator',
        is_file_md: true,
        position: 4,
      })
      let fileTemplate = server.create('playouthistorytemplate', {
        type: 'file',
      })
      fileTemplate.createField({
        name: 'track_title',
        label: 'Title',
        is_file_md: true,
        position: 1,
      })
      fileTemplate.createField({
        name: 'artist_name',
        label: 'Creator',
        is_file_md: true,
        position: 2,
      })
      fileTemplate.createField({
        name: 'played',
        label: 'Played',
        is_file_md: false,
        position: 1,
      })
      fileTemplate.createField({
        name: 'duration',
        label: 'Duration',
        is_file_md: true,
        position: 2,
      })
      fileTemplate.createField({
        name: 'metadata.showname',
        label: 'Show',
        is_file_md: false,
        position: 3,
      })
      itemTemplate.createField({
        name: 'metadata.artist_name',
        label: 'Show Creator',
        is_file_md: false,
        position: 4,
      })
    },
    routes() {
      this.namespace = 'api/v2'

      this.get('/playout-history', (schema) => {
        return schema.playouthistories.all()
      })
      this.get('/playout-history-template', (schema) => {
        return schema.playouthistorytemplates.all()
      })
    },
  })

  return server
}
