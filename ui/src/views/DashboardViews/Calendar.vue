<template>
  <calendar />
</template>

<script lang="ts">
import { defineComponent } from "vue";

// Components
import Calendar from "@/components/Dashboard/Calendar.vue";

export default defineComponent({
  name: "CalendarView",

  components: {
    Calendar,
  },

  data: () => ({
    items: [
      {
        title: "Foo",
        value: "foo",
      },
      {
        title: "Bar",
        value: "bar",
      },
      {
        title: "Fizz",
        value: "fizz",
      },
      {
        title: "Buzz",
        value: "buzz",
      },
    ],
  }),
});
</script>


<style>

</style>