<template>
  <div class="px-2">
    <p class="text-h3 ma-0">{{ currentTime }}</p>
    <div class="body-2 ma-0">
      <p>Username | Logout</p>
    </div>
  </div>
</template>

<script>
import dayjs from 'dayjs'
export default {
  name: 'StationClock',
  computed: {
    currentTime: () => {
      return dayjs().format('H:mm')
    },
  },
}
</script>
