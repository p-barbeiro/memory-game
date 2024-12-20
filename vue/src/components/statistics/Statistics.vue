<template>
  <div class="w-full">
    <div class="flex justify-center">
      <div class="flex flex-row items-center gap-3 justify-center md:w-1/2">
        <span class="text-sm text-nowrap"> Start Date: </span>
        <VueDatePicker @blur="fetchPersonalStatistics" v-model="startDate" :format="format"></VueDatePicker>
        <span class="text-sm text-nowrap"> End Date: </span>
        <VueDatePicker @blur="fetchPersonalStatistics" v-model="endDate" :format="format"></VueDatePicker>
      </div>
    </div>
    <hr class="my-3" />

    <h1 class="text-2xl font-semibold text-center text-indigo-900">Singleplayer Statistics</h1>

    <hr class="my-3" />

    <div class="flex flex-col md:flex-row w-full gap-3">
      <div v-if="singleplayerTimes" class="border rounded-xl p-2 shadow md:w-1/2">
        <h2 class="text-lg font-semibold text-center">Times Statistics</h2>
        <hr class="my-2" />
        <div>
          <Bar :data="singleplayerTimes" />
        </div>
      </div>
      <div v-if="singleplayerTurns" class="border rounded-xl p-2 shadow md:w-1/2">
        <h2 class="text-lg font-semibold text-center">Turns Statistics</h2>
        <hr class="my-2" />
        <div>
          <Bar :data="singleplayerTurns" />
        </div>
      </div>
    </div>

    <div>
      <div class="mt-5 flex flex-col md:flex-row w-full gap-3">
        <div v-if="totalGamesPieChart" class="border rounded-xl p-2 shadow md:w-1/3">
          <h2 class="text-lg font-semibold text-center">Total Games Played</h2>
          <hr class="my-2" />
          <div>
            <Pie :data="totalGamesPieChart" />
          </div>
        </div>

        <div v-if="totalTimePlayedPieChart" class="border rounded-xl p-2 shadow md:w-1/3">
          <h2 class="text-lg font-semibold text-center">Total Time Played</h2>
          <hr class="my-2" />
          <div>
            <Pie :data="totalTimePlayedPieChart" />
          </div>
        </div>

        <div v-if="apiData" class="border rounded-xl p-2 shadow md:w-1/3">
          <h2 class="text-lg font-semibold text-center mb-3">Summary</h2>
          <Table>
            <TableBody v-for="(board, index) in apiData.games.singlePlayer.boards" :key="index">
              <TableRow class="flex justify-center text-base text-gray-700 font-bold border rounded p-1 bg-indigo-50">{{ index }}</TableRow>
              <TableRow class="flex p-1">
                Total Games Played:
                <span class="ml-auto">
                  {{ board.totalGames }}
                </span>
              </TableRow>
              <TableRow class="flex p-1">
                Total Time Played:
                <span class="ml-auto"> {{ board.totalTimePlayed }} min </span>
              </TableRow>
              <TableRow class="flex p-1">
                Average Time:
                <span class="ml-auto"> {{ board.averageTime }} s </span>
              </TableRow>
              <TableRow class="flex p-1">
                Average Turns:
                <span class="ml-auto">
                  {{ board.averageTurns }}
                </span>
              </TableRow>
            </TableBody>
          </Table>
        </div>
      </div>
    </div>

    <hr class="my-3" />
    <!-- Multiplayer Stats -->
    <h1 class="text-2xl font-semibold text-center text-indigo-900">Multiplayer Statistics</h1>

    <hr class="my-3" />

    <div class="flex">
      <div class="flex flex-col md:flex-row w-full gap-3">
        <!-- Multiplayer Total Pairs Discovered -->
        <div v-if="multiPlayerStats" class="border rounded-xl p-2 shadow md:w-1/2">
          <h2 class="text-lg font-semibold text-center">Pairs Discovered</h2>
          <hr class="my-2" />
          <div>
            <Bar :data="multiPlayerStats" />
          </div>
        </div>

        <!-- Multiplayer Total Games Stats -->
        <div v-if="multiPlayerOverview" class="border rounded-xl p-2 shadow md:w-1/2">
          <h2 class="text-lg font-semibold text-center">Multiplayer Summary</h2>
          <hr class="my-2" />
          <div>
            <Table>
              <TableBody>
                <TableRow class="flex justify-center text-base text-gray-700 font-bold border rounded p-1 bg-indigo-50">Overview</TableRow>
                <TableRow class="flex p-1">
                  Total Games Played:
                  <span class="ml-auto">{{ multiPlayerOverview.totalGames }}</span>
                </TableRow>
                <TableRow class="flex p-1">
                  Wins:
                  <span class="ml-auto">{{ multiPlayerOverview.wins }}</span>
                </TableRow>
                <TableRow class="flex p-1">
                  Loses:
                  <span class="ml-auto">{{ multiPlayerOverview.loses }}</span>
                </TableRow>
                <TableRow class="flex p-1">
                  Win Rate:
                  <span class="ml-auto">{{ multiPlayerOverview.winRate }}</span>
                </TableRow>
                <TableRow class="flex p-1">
                  Total Pairs Discovered:
                  <span class="ml-auto">{{ multiPlayerOverview.totalPairsDiscovered }}</span>
                </TableRow>
              </TableBody>
            </Table>
          </div>
        </div>
      </div>

      <!-- Multiplayer Pairs Discovered Pie Chart (Optional) -->
      <div v-if="multiPlayerPieChart" class="border rounded-xl p-2 shadow md:w-1/2">
        <h2 class="text-lg font-semibold text-center">Pairs Discovered (Pie Chart)</h2>
        <hr class="my-2" />
        <div>
          <Pie :data="multiPlayerPieChart" />
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useAuthStore } from '@/stores/auth'
import { useErrorStore } from '@/stores/error'
import VueDatePicker from '@vuepic/vue-datepicker'
import '@vuepic/vue-datepicker/dist/main.css'
import axios from 'axios'
import { ArcElement, BarElement, CategoryScale, Chart as ChartJS, Legend, LinearScale, Title, Tooltip } from 'chart.js'
import { onMounted, ref } from 'vue'
import { Bar, Pie } from 'vue-chartjs'
import Table from '../ui/table/Table.vue'
import TableBody from '../ui/table/TableBody.vue'
import TableRow from '../ui/table/TableRow.vue'

ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale)
ChartJS.register(ArcElement, Tooltip, Legend)

const storeAuth = useAuthStore()
const storeError = useErrorStore()

const singleplayerTimes = ref(null)
const singleplayerTurns = ref(null)
const totalGamesPieChart = ref(null)
const totalTimePlayedPieChart = ref(null)
const multiPlayerOverview = ref(null)
const multiPlayerStats = ref(null)
const apiData = ref(null)
const isLoading = ref(false)
const startDate = ref(Date.now() - 31536000000) // 1 year in milliseconds
const endDate = ref()

const formatDate = (date) => {
  return new Date(date).toISOString().split('T')[0]
}

const format = (date) => {
  const day = date.getDate()
  const month = date.getMonth() + 1
  const year = date.getFullYear()

  return `${year}/${month}/${day}`
}

const fetchPersonalStatistics = async () => {
  storeError.resetMessages()
  try {
    isLoading.value = true
    axios.defaults.headers.common.Authorization = 'Bearer ' + storeAuth.token
    axios.defaults.headers.common['Content-Type'] = 'application/json'

    const queryParams = new URLSearchParams({
      ...(startDate.value && { start_date: formatDate(startDate.value) }),
      ...(endDate.value && { end_date: formatDate(endDate.value) })
    }).toString()

    const response = await axios.get(`statistics/personal?${queryParams}`)

    if (response.status !== 200) {
      throw response
    }

    apiData.value = response.data

    const singleplayer = apiData.value.games.singlePlayer
    singleplayerTimes.value = {
      labels: ['Board 3 x 4', 'Board 4 x 4', 'Board 6 x 6'],
      datasets: [
        {
          label: 'Min. Time (s)',
          backgroundColor: 'rgba(129, 140, 248, 0.75)',
          borderWidth: 1,
          borderColor: 'rgba(129, 140, 248, 1)',
          data: [singleplayer.boards['3x4']?.minTime ?? null, singleplayer.boards['4x4']?.minTime ?? null, singleplayer.boards['6x6']?.minTime ?? null]
        },
        {
          label: 'Average. Time (s)',
          backgroundColor: 'rgba(99,102,241,0.75)',
          borderWidth: 1,
          borderColor: 'rgba(99,102,241,1)',
          data: [singleplayer.boards['3x4']?.averageTime ?? null, singleplayer.boards['4x4']?.averageTime ?? null, singleplayer.boards['6x6']?.averageTime ?? null]
        },
        {
          label: 'Max. Time (s)',
          backgroundColor: 'rgba(55,48,163,0.75)',
          borderWidth: 1,
          borderColor: 'rgba(55,48,163,1)',
          data: [singleplayer.boards['3x4']?.maxTime ?? null, singleplayer.boards['4x4']?.maxTime ?? null, singleplayer.boards['6x6']?.maxTime ?? null]
        }
      ]
    }

    singleplayerTurns.value = {
      labels: ['Board 3 x 4', 'Board 4 x 4', 'Board 6 x 6'],
      datasets: [
        {
          label: 'Min. Turns',
          backgroundColor: 'rgba(129, 140, 248, 0.75)',
          borderWidth: 1,
          borderColor: 'rgba(129, 140, 248, 1)',
          data: [singleplayer.boards['3x4']?.minTurns ?? null, singleplayer.boards['4x4']?.minTurns ?? null, singleplayer.boards['6x6']?.minTurns ?? null]
        },
        {
          label: 'Average. Turns',
          backgroundColor: 'rgba(99,102,241,0.75)',
          borderWidth: 1,
          borderColor: 'rgba(99,102,241,1)',
          data: [singleplayer.boards['3x4']?.averageTurns ?? null, singleplayer.boards['4x4']?.averageTurns ?? null, singleplayer.boards['6x6']?.averageTurns ?? null]
        },
        {
          label: 'Max. Turns',
          backgroundColor: 'rgba(55,48,163,0.75)',
          borderWidth: 1,
          borderColor: 'rgba(55,48,163,1)',
          data: [singleplayer.boards['3x4']?.maxTurns ?? null, singleplayer.boards['4x4']?.maxTurns ?? null, singleplayer.boards['6x6']?.maxTurns ?? null]
        }
      ]
    }

    totalGamesPieChart.value = {
      labels: ['Board 3 x 4', 'Board 4 x 4', 'Board 6 x 6'],
      datasets: [
        {
          label: 'Total Games',
          backgroundColor: ['rgba(129, 140, 248, 0.75)', 'rgba(99,102,241,0.75)', 'rgba(55,48,163,0.75)'],
          data: [singleplayer.boards['3x4']?.totalGames ?? 0, singleplayer.boards['4x4']?.totalGames ?? 0, singleplayer.boards['6x6']?.totalGames ?? 0]
        }
      ]
    }

    totalTimePlayedPieChart.value = {
      labels: ['Board 3 x 4', 'Board 4 x 4', 'Board 6 x 6'],
      datasets: [
        {
          label: 'Total Time Played (s)',
          backgroundColor: ['rgba(129, 140, 248, 0.75)', 'rgba(99,102,241,0.75)', 'rgba(55,48,163,0.75)'],
          data: [singleplayer.boards['3x4']?.totalTimePlayed ? parseInt(singleplayer.boards['3x4'].totalTimePlayed) : 0, singleplayer.boards['4x4']?.totalTimePlayed ? parseInt(singleplayer.boards['4x4'].totalTimePlayed) : 0, singleplayer.boards['6x6']?.totalTimePlayed ? parseInt(singleplayer.boards['6x6'].totalTimePlayed) : 0]
        }
      ]
    }

    const multiPlayer = apiData.value.games.multiPlayer
    multiPlayerStats.value = {
      labels: ['Board 3 x 4', 'Board 4 x 4', 'Board 6 x 6'],
      datasets: [
        {
          label: 'Wins',
          backgroundColor: ['rgba(129, 140, 248, 0.75)', 'rgba(99,102,241,0.75)', 'rgba(55,48,163,0.75)'],
          data: [multiPlayer.boards[0]?.boardWins ?? 0, multiPlayer.boards[1]?.boardWins ?? 0, multiPlayer.boards[2]?.boardWins ?? 0]
        },
        {
          label: 'Losses',
          backgroundColor: 'rgba(255, 99, 132, 0.75)',
          data: [multiPlayer.boards[0]?.boardLoses ?? null, multiPlayer.boards[1]?.boardLoses ?? null, multiPlayer.boards[2]?.boardLoses ?? null]
        }
      ]
    }

    multiPlayerOverview.value = {
      totalGames: multiPlayer?.totalGames ?? 0,
      wins: multiPlayer?.wins ?? 0,
      loses: multiPlayer?.loses ?? 0,
      winRate: multiPlayer?.winRate ?? '0%',
      totalPairsDiscovered: multiPlayer?.totalPairsDiscovered ?? 0
    }

    isLoading.value = false
  } catch (e) {
    storeError.setErrorMessages(e.response.data.message, e.response.data.errors, e.response.status, 'Error fetching personal statistics!')
    isLoading.value = false
    return false
  }
}

// Buscar dados ao montar o componente
onMounted(async () => {
  await fetchPersonalStatistics()
})
</script>
