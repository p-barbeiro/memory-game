<script setup>
import { useGameStore } from '@/stores/game'
import { computed, onMounted, ref, watch } from 'vue'
import Dropdown from '../ui/dropdown/Dropdown.vue'
import DataTable from '../common/DataTable.vue'
import Pagination from '../common/Pagination.vue'
const gameStore = useGameStore()

//Sort options
const sortOptions = [
  { value: 'id', label: 'None' },
  { value: 'date', label: 'Date' },
  { value: 'time', label: 'Game Time' },
  { value: 'turns', label: 'Total Turns' }
]
const selectedSortOption = ref(sortOptions[0].value)
watch(selectedSortOption, (newValue) => {
  console.log('Sort:', newValue)
  updateGames()
})

//Game mode filter options
const gameModeOptions = [
  { value: 'S', label: 'Singleplayer' },
  { value: 'M', label: 'Multiplayer' }
]
const gameModeFilterOption = ref(gameModeOptions[0].value)
watch(gameModeFilterOption, (newValue) => {
  console.log('Gamemode:', newValue)
  updateGames()
  if (newValue === 'M') {
    columns.value = [
      { title: 'Game type', key: 'type' },
      { title: 'Created By', key: 'create_user_id', subkey: 'nickname' },
      { title: 'Winner', key: 'winner_user_id', subkey: 'nickname' },
      { title: 'Opponent', key: 'opponent_user_id', subkey: 'nickname' },
      { title: 'Start date', key: 'start_date' },
      { title: 'Start time', key: 'start_time' },
      { title: 'Board', key: 'board', subkey: 'name' },
      { title: 'Status', key: 'status' },
      { title: 'Game Time', key: 'total_time', append: 's' },
      { title: 'Total Turns', key: 'turns' }
    ]
  } else {
    columns.value = [
      { title: 'Game type', key: 'type' },
      { title: 'Start date', key: 'start_date' },
      { title: 'Start time', key: 'start_time' },
      { title: 'Board', key: 'board', subkey: 'name' },
      { title: 'Status', key: 'status' },
      { title: 'Game Time', key: 'total_time', append: 's' },
      { title: 'Total Turns', key: 'turns' }
    ]
  }
})

//Board filter options
const boardOptions = [
  { value: '', label: 'All' },
  { value: '1', label: '3x4' },
  { value: '2', label: '4x4' },
  { value: '3', label: '6x6' }
]
const boardFilterOption = ref(boardOptions[0].value)
watch(boardFilterOption, (newValue) => {
  console.log('Board:', newValue)
  updateGames()
})

//Status filter options
const statusOptions = [
  { value: '', label: 'All' },
  { value: 'PE', label: 'Pending' },
  { value: 'PL', label: 'Playing' },
  { value: 'E', label: 'Ended' },
  { value: 'I', label: 'Interrupted' }
]
const statusFilterOption = ref(statusOptions[0].value)
watch(statusFilterOption, (newValue) => {
  console.log('Status:', newValue)
  updateGames()
})

//Table columns
const columns = ref([
  { title: 'Game type', key: 'type' },
  { title: 'Start date', key: 'start_date' },
  { title: 'Start time', key: 'start_time' },
  { title: 'Board', key: 'board', subkey: 'name' },
  { title: 'Status', key: 'status' },
  { title: 'Game Time', key: 'total_time', append: 's' },
  { title: 'Total Turns', key: 'turns' },
  { title: 'Game', link: true, linkText: 'Open', linkTo: '/game/:id' }
])

//Pagination
const goToPage = ref(1)
const currentPage = computed(() => gameStore.meta.current_page)
const lastPage = computed(() => gameStore.meta.last_page)
const nextPage = () => {
  goToPage.value = currentPage.value + 1
  updateGames()
}

const previousPage = () => {
  goToPage.value = currentPage.value - 1
  updateGames()
}

const changePage = (page) => {
  goToPage.value = page
  updateGames()
}

const updateGames = () => {
  gameStore.fetchGames(goToPage.value, selectedSortOption.value, gameModeFilterOption.value, boardFilterOption.value, statusFilterOption.value)
}

onMounted(async () => {
  gameStore.fetchGames(1, 'id')
})
</script>

<template>
  <div class="flex flex-col h-full w-full justify-center">
    <h1 class="text-2xl font-semibold mb-5">Game History</h1>

    <div class="flex flex-col-reverse md:flex-row justify-between">
      <div>
        <Pagination :currentPage="currentPage" :lastPage="lastPage" @previousPage="previousPage" @changePage="changePage" @nextPage="nextPage" />
      </div>

      <div class="flex flex-col md:flex-row gap-3">
        <Dropdown text="Game status:" v-model="statusFilterOption" :options="statusOptions" />
        <Dropdown text="Game mode:" v-model="gameModeFilterOption" :options="gameModeOptions" />
        <Dropdown text="Board:" v-model="boardFilterOption" :options="boardOptions" />
        <Dropdown text="Sort By:" v-model="selectedSortOption" :options="sortOptions" />
      </div>
    </div>

    <div class="mt-3 overflow-x-auto">
      <DataTable :data="gameStore.games" :columns="columns" />
    </div>
  </div>
</template>