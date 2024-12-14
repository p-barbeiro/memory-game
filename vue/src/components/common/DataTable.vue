<template>
  <div class="rounded-lg">
    <table v-if="data.length" class="w-full whitespace-nowrap table-auto border">
      <thead class="bg-gray-50 border-b">
        <th v-for="column in columns" class="text-center px-2 text-sm text-gray-900 py-3 font-normal">{{ column.title }}</th>
      </thead>
      <tbody>
        <TableRow v-for="dataUnit in data" :model="dataUnit" :columns="columns" />
      </tbody>
    </table>
    <div v-else class="text-center text-gray-400 py-10 border-y">No data available</div>
  </div>
</template>

<script setup>
import TableRow from './TableRow.vue'

const props = defineProps({
  data: {
    type: Array,
    required: true
  },
  columns: {
    type: Array,
    required: true
  }
})
</script>
