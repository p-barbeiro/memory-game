<template>
  <div class="flex flex-col w-full">
    <div class="flex justify-center">
      <div class="flex flex-row items-center gap-3 justify-center md:w-1/2">
        <span class="text-sm text-nowrap"> Start Date: </span>
        <VueDatePicker @blur="applyDateFilter" v-model="startDate"></VueDatePicker>
        <span class="text-sm text-nowrap"> End Date: </span>
        <VueDatePicker @blur="applyDateFilter" v-model="endDate"></VueDatePicker>
      </div>
    </div>

    <hr class="my-3" />
  

    <div class="flex gap-3">
      <div v-if="topPlayersData" class="border rounded-md p-3 w-1/2">
        <h3 class="text-lg font-semibold">Top Players by Wins</h3>
        <Bar :data="topPlayersData" />
      </div>

      <div v-if="purchasesByPlayerData" class="border rounded-md p-3 w-1/2">
        <h3 class="text-lg font-semibold">Purchases by Player</h3>
        <Bar :data="purchasesByPlayerData" />
      </div>
    </div>
    <div class="flex gap-3 mt-3">
      <div v-if="filteredPurchasesByDateData" class="border rounded-md p-3 w-1/2">
        <h3 class="text-lg font-semibold">Purchases by Date</h3>
        <Line :data="filteredPurchasesByDateData" />
      </div>

      <div v-if="filteredGamesByDayData" class="border rounded-md p-3 w-1/2">
        <h3 class="text-lg font-semibold">Games Played by Day</h3>
        <Line :data="filteredGamesByDayData" />
      </div>
    </div>

    <div v-if="filteredTransactionsByDayData" class="border rounded-md p-3 mt-3">
      <h3 class="text-lg font-semibold">Transactions by Day</h3>
      <Line :data="filteredTransactionsByDayData" />
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
  import VueDatePicker from '@vuepic/vue-datepicker'
  import '@vuepic/vue-datepicker/dist/main.css'
import { Bar, Line } from 'vue-chartjs'
import { Chart as ChartJS, Title, Tooltip, Legend, CategoryScale, LinearScale, PointElement, LineElement, BarElement } from 'chart.js'
import { useAuthStore } from '@/stores/auth'
import { useErrorStore } from '@/stores/error'
import axios from 'axios'

const isLoading = ref(false)
const storeAuth = useAuthStore()
const storeError = useErrorStore()

ChartJS.register(Title, Tooltip, Legend, CategoryScale, LinearScale, PointElement, LineElement, BarElement)

const rawData = reactive({
  purchasesByDate: [],
  purchasesByPlayer: [],
  topPlayers: [],
  gamesByDay: [],
  transactionsByDay: []
})

const startDate = ref(Date.now()-1000*60*60*24*30)
const endDate = ref(Date.now())
const filteredPurchasesByDateData = ref(null)
const filteredGamesByDayData = ref(null)
const filteredTransactionsByDayData = ref(null)

// Fetch Data
const fetchAdminStatistics = async () => {
  storeError.resetMessages()
  try {
    isLoading.value = true
    axios.defaults.headers.common.Authorization = 'Bearer ' + storeAuth.token
    axios.defaults.headers.common['Content-Type'] = 'application/json'

    const response = await axios.get(`statistics/admin`)

    if (response.status !== 200) {
      throw response
    }
    const data = response.data

    rawData.purchasesByDate = data.purchasesByDate
    rawData.purchasesByPlayer = data.purchasesByPlayer
    rawData.topPlayers = data.topPlayers
    rawData.gamesByDay = data.gamesByDay
    rawData.transactionsByDay = data.transactionsByDay
    filteredPurchasesByDateData.value = filterChartData(rawData.purchasesByDate, startDate.value, endDate.value)
    filteredGamesByDayData.value = filterChartData(rawData.gamesByDay, startDate.value, endDate.value)
    filteredTransactionsByDayData.value = filterChartData(rawData.transactionsByDay, startDate.value, endDate.value)

    isLoading.value = false
  } catch (e) {
    storeError.setErrorMessages(e.response.data.message, e.response.data.errors, e.response.status, 'Error fetching personal statistics!')
    isLoading.value = false
    return false
  }
}


// Filters
const applyDateFilter = () => {
  const start = new Date(startDate.value)
  const end = new Date(endDate.value)

  if (start > end) {
    alert('Start date must be before the end date!')
    return
  }

  filteredPurchasesByDateData.value = filterChartData(rawData.purchasesByDate, start, end)
  filteredGamesByDayData.value = filterChartData(rawData.gamesByDay, start, end)
  filteredTransactionsByDayData.value = filterChartData(rawData.transactionsByDay, start, end)
}

const filterChartData = (data, start, end) => {
  const filtered = data.filter((item) => {
    const itemDate = new Date(item.date || item.day)
    return itemDate >= start && itemDate <= end
  })

  return {
    labels: filtered.map((item) => item.date || item.day),
    datasets: [
      {
        label: 'Total',
        data: filtered.map((item) => item.total || item.total_games || item.total_amount || 0),
        borderColor: 'rgba(75, 192, 192, 1)',
        backgroundColor: 'rgba(75, 192, 192, 0.2)',
        fill: true
      }
    ]
  }
}

// Chart Data Computed Properties
const purchasesByPlayerData = computed(() => ({
  labels: rawData.purchasesByPlayer.map((item) => item.nickname),
  datasets: [
    {
      label: 'Total Purchases',
      data: rawData.purchasesByPlayer.map((item) => item.total),
      backgroundColor: 'rgba(54, 162, 235, 0.5)'
    }
  ]
}))

const topPlayersData = computed(() => ({
  labels: rawData.topPlayers.map((item) => item.nickname),
  datasets: [
    {
      label: 'Total Wins',
      data: rawData.topPlayers.map((item) => item.total_wins),
      backgroundColor: 'rgba(255, 206, 86, 0.5)'
    }
  ]
}))



onMounted(() => {
  fetchAdminStatistics()
  applyDateFilter()
})
</script>
