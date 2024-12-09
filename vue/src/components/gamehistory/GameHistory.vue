<template>
  <div class="w-full p-8 h-[calc(100%-6rem)] md:rounded-xl md:shadow bg-white">
    <div v-if="loading">
        <IconLoading class="w-10 h-10 text-indigo-950 animate-spin" />
    </div>
    <div class="flex flex-col h-full justify-center">
      <div class="flex items-center justify-between">
        <div class="text-2xl font-bold text-gray-800 mb-3">Game History</div>
      </div>

      <div>
        <div class="flex flex-row items-center justify-between">
          <div class="flex items-center gap-3">
            <div @click="activate('all')" class="rounded-full cursor-pointer text-gray-500 px-4 py-1 hover:text-indigo-950" :class="{ 'bg-indigo-950 hover:text-white text-white': filterAll }">All</div>
            <div @click="activate('sp')" class="rounded-full cursor-pointer text-gray-500 px-4 py-1 hover:text-indigo-950" :class="{ 'bg-indigo-950  hover:text-white text-white': filterSingleplayer }">Single-player</div>
            <div @click="activate('mp')" class="rounded-full cursor-pointer text-gray-500 px-4 py-1 hover:text-indigo-950" :class="{ 'bg-indigo-950  hover:text-white text-white': filterMultiplayer }">Multiplayer</div>
          </div>
          <div class="flex items-center text-gray-600 text-sm">
            <div class="text-gray-400 text-nowrap mr-3">Sort By:</div>
            <Dropdown :modelValue="dropdownOption" :options="dropdownOptions" class="w-full" />
          </div>
        </div>
        <div class="mt-3 overflow-x-auto">
          <table class="w-full whitespace-nowrap">
            <thead class="border-t border-b mb-3">
              <th v-for="column in columns" class="text-center text-sm font-semibold text-gray-800 py-3">{{ column.title }}</th>
            </thead>
            <tbody>
              <TableRow v-for="game in gameStore.games" :model="game" :columns="columns" />
            </tbody>
          </table>
          <div class="mb-4">Showing page {{ currentPage }} of {{ totalPages }}</div>
          <Pagination :totalItems="10" :itemsPerPage="10" :currentPageProp="currentPage" @update:currentPage="updatePage" />
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useGameStore } from '@/stores/game'
import { onMounted, ref, computed } from 'vue'
import IconTimer from '../icons/IconTimer.vue'
import Dropdown from '../ui/dropdown/Dropdown.vue'
import TableRow from './TableRow.vue'
import Pagination from './Pagination.vue'
import IconLoading from '../icons/IconLoading.vue'
const gameStore = useGameStore()

const filterAll = ref(true)
const filterSingleplayer = ref(false)
const filterMultiplayer = ref(false)

const currentPage = ref(1)
const totalItems = 100
const itemsPerPage = 10

const updatePage = (page) => {
  currentPage.value = page
  console.log(`Current page: ${page}`)
}

const activate = (type) => {
  filterAll.value = false
  filterSingleplayer.value = false
  filterMultiplayer.value = false
  switch (type) {
    case 'all':
      filterAll.value = true
      break
    case 'sp':
      filterSingleplayer.value = true
      break
    case 'mp':
      filterMultiplayer.value = true
      break
  }
}

const dropdownOptions = [
  { value: 'all', label: 'All' },
  { value: 'done', label: 'Done' },
  { value: 'pending', label: 'Pending' }
]
const dropdownOption = ref(dropdownOptions[0].value)

const columns = [
  { title: 'Game', key: 'id' },
  { title: 'Time', key: 'total_time' },
  { title: 'Date', key: 'start_date' },
  { title: 'Moves', key: 'turns' },
  { title: 'Status', key: 'status' }
]

const totalPages = computed(() => {
  return gameStore.gamesWithPagination.meta.total
})

onMounted(async () => {
  gameStore.fetchGames()
  console.log('GameHistory mounted')
})
</script>
