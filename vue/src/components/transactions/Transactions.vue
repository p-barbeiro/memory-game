<template>
  <div class="flex flex-col h-full w-full justify-center">
    <h1 class="text-2xl font-semibold mb-5">Transactions</h1>

    <div class="flex flex-col-reverse md:flex-row justify-between">
      <div>
        <Pagination :currentPage="currentPage" :lastPage="lastPage" @previousPage="previousPage" @changePage="changePage" @nextPage="nextPage" />
      </div>

      <div class="flex flex-col md:flex-row gap-3">
        <Dropdown text="Transaction type:" v-model="transactionFilterOption" :options="transactionOptions" />
        <Dropdown text="Sort By:" v-model="selectedSortOption" :options="sortOptions" />
      </div>
    </div>

    <div class="mt-3 overflow-x-auto">
      <DataTable :data="transactionStore.transactions" :columns="columns" />
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted, ref, watch } from 'vue'
import Dropdown from '../ui/dropdown/Dropdown.vue'
import DataTable from '../common/DataTable.vue'
import Pagination from '../common/Pagination.vue'
import { useTransactionStore } from '@/stores/transaction'

const transactionStore = useTransactionStore()

//Sort options
const sortOptions = [
  { value: 'date', label: 'Date' },
  { value: 'coins', label: 'Brain coins' }
]
const selectedSortOption = ref(sortOptions[0].value)
watch(selectedSortOption, (newValue) => {
  console.log('Sort:', newValue)
  updateFetch()
})

//Status filter options
const transactionOptions = [
  { value: '', label: 'All' },
  { value: 'I', label: 'In-Game' },
  { value: 'P', label: 'Purchase' },
  { value: 'B', label: 'Bonus' }
]
const transactionFilterOption = ref(transactionOptions[0].value)
watch(transactionFilterOption, (newValue) => {
  console.log('Transaction type:', newValue)
  updateFetch()
  switch (newValue) {
    case 'I':
      columns.value = [
        { title: 'Transaction', key: 'type' },
        { title: 'Date', key: 'date' },
        { title: 'Time', key: 'time' },
        { title: 'Brain Coins', key: 'brain_coins' },
      ]
      break
    case 'P':
      columns.value = [
        { title: 'Transaction', key: 'type' },
        { title: 'Date', key: 'date' },
        { title: 'Time', key: 'time' },
        { title: 'Brain Coins', key: 'brain_coins' },
        { title: 'Amount', key: 'euros', append: '€' },
        { title: 'Payment Type', key: 'payment_type' },
        { title: 'Payment Reference', key: 'payment_reference' }
      ]
      break
    case 'B':
      columns.value = [
        { title: 'Transaction', key: 'type' },
        { title: 'Date', key: 'date' },
        { title: 'Time', key: 'time' },
        { title: 'Brain Coins', key: 'brain_coins' }
      ]
      break
    default:
      columns.value = [
        { title: 'Transaction', key: 'type' },
        { title: 'Date', key: 'date' },
        { title: 'Time', key: 'time' },
        { title: 'Brain Coins', key: 'brain_coins' },
        { title: 'Amount', key: 'euros', append: '€' },
        { title: 'Payment Type', key: 'payment_type' },
        { title: 'Payment Reference', key: 'payment_reference' }
      ]
  }
})

//Table columns
const columns = ref([
  { title: 'Transaction', key: 'type' },
  { title: 'Date', key: 'date' },
  { title: 'Time', key: 'time' },
  { title: 'Brain Coins', key: 'brain_coins' },
  { title: 'Amount', key: 'euros', append: '€' },
  { title: 'Payment Type', key: 'payment_type' },
  { title: 'Payment Reference', key: 'payment_reference' }
])

//Pagination
const goToPage = ref(1)
const currentPage = computed(() => transactionStore.meta.current_page)
const lastPage = computed(() => transactionStore.meta.last_page)
const nextPage = () => {
  goToPage.value = currentPage.value + 1
  updateFetch()
}

const previousPage = () => {
  goToPage.value = currentPage.value - 1
  updateFetch()
}

const changePage = (page) => {
  goToPage.value = page
  updateFetch()
}

const updateFetch = () => {
  transactionStore.fetchTransactions(goToPage.value, selectedSortOption.value, transactionFilterOption.value)
}

onMounted(async () => {
  transactionStore.fetchTransactions(1, 'date')
})
</script>
