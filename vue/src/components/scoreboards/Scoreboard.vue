<template>
  <div class="flex flex-col h-full w-full justify-center">
    <h1 class="text-2xl font-semibold mb-5">Game History</h1>

    <div class="mt-3 overflow-x-auto">
      <DataTable :data="gameStore.games" :columns="columns" />
    </div>
  </div>
</template>

<script setup>
import { useGameStore } from '@/stores/game'
import { onMounted, ref } from 'vue'
import DataTable from '../common/DataTable.vue'
const gameStore = useGameStore()

//Table columns
const columns = ref([
  { title: 'Game type', key: 'type' },
  { title: 'Start date', key: 'start_date' },
  { title: 'Start time', key: 'start_time' },
  { title: 'Board', key: 'board', subkey: 'name'},
  { title: 'Status', key: 'status' },
  { title: 'Game Time', key: 'total_time', append: 's' },
  { title: 'Total Turns', key: 'turns' },
  { title: 'Game', link: true, linkText: 'Open', linkTo: '/game/:id' }
])

onMounted(async () => {
  gameStore.fetchGames(1, 'time','S',1,'E')
})
</script>
