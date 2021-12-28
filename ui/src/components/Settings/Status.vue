<template>
  <div>
    <v-simple-table>
      <template v-slot:default>
        <thead>
          <tr>
            <th class="text-left">Service</th>
            <th>Description</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="service in services" :key="service.name">
            <td>{{ service.name }}</td>
            <td>{{ service.description }}</td>
            <td>{{ service.status }}</td>
          </tr>
        </tbody>
      </template>
    </v-simple-table>
    <p>Libretime version: {{ version }}</p>
  </div>
</template>

<script>
export default {
  name: 'Status',
  data() {
    return {
      services: [
        { name: 'Postgres', description: 'PDO and PostgreSQL libraries', status: 'Good' },
        { name: 'Database', description: 'Database configuration for LibreTime', status: 'Good' },
        { name: 'RabbitMQ', description: 'RabbitMQ configuration for LibreTime', status: 'Good' },
        { name: 'Media Analyzer', description: 'LibreTime media analyzer service', status: 'Good' },
        { name: 'Pypo', description: 'LibreTime playout service', status: 'Good' },
        { name: 'Liquidsoap', description: 'LibreTime liquidsoap service', status: 'Good' },
        { name: 'Celery', description: 'LibreTime Celery Task service', status: 'Good' },
        { name: 'API', description: 'LibreTime API service', status: 'Good' },
      ],
      version: '4.0.0-dev',
    }
  },
}
</script>
