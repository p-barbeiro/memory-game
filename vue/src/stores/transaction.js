import { useToast } from '@/components/ui/toast/use-toast'
import { useErrorStore } from '@/stores/error'
import axios from 'axios'
import { defineStore } from 'pinia'
import { computed, ref } from 'vue'
import { useAuthStore } from './auth'
import { useRouter } from 'vue-router'

export const useTransactionStore = defineStore('transaction', () => {
  const storeAuth = useAuthStore()
  const storeError = useErrorStore()
  const router = useRouter()

  const { toast } = useToast()

  const transactions = ref([])
  const totalTransactions = ref(0)
  const page = ref(1)
  const filters = ref({
    type: '',
    search: '',
    sort_by: 'id',
    sort_direction: 'desc'
  })

  const resetPage = () => {
    page.value = 1
    transactions.value = null
  }

  const fetchTransactions = async (resetPagination = false) => {
    storeError.resetMessages()
    try {
      if (resetPagination) {
        resetPage()
      }

      const queryParams = new URLSearchParams({
        page: page.value,
        ...(filters.value.type && { type: filters.value.type }),
        ...(filters.value.search && { search: filters.value.search }),
        sort_by: filters.value.sort_by,
        sort_direction: filters.value.sort_direction
      }).toString()

      axios.defaults.headers.common.Authorization = 'Bearer ' + storeAuth.token
      axios.defaults.headers.common['Content-Type'] = 'application/json'

      const response = await axios.get(`transactions?${queryParams}`)

      if (response.status !== 200) {
        throw response
      }
      totalTransactions.value = response.data.meta.total

      if (page.value === 1 || resetPagination) {
        transactions.value = response.data.data
      } else {
        transactions.value = [...(transactions.value || []), ...response.data.data]
      }

      return response.data
    } catch (e) {
      storeError.setErrorMessages(e.response.data.message, e.response.data.errors, e.response.status, 'Fetch Users Error')
      return false
    }
  }
  
  const fetchTransactionsNextPage = async () => {
    page.value++
    await fetchTransactions()
  }
  
  const createTransaction = async (params) => {
    storeError.resetMessages()
    try {
      axios.defaults.headers.common.Authorization = 'Bearer ' + storeAuth.token
      axios.defaults.headers.common['Content-Type'] = 'application/json'

      const body = {
        ...params
      }
      const response = await axios.post(`transactions`, body)
      //console.log(response)

      if (response.status === 201) {
        const transaction = response.data.data
        console.log(transaction)
        var message = ''
        switch (params.type) {
          case 'I':
            message = `Board Unlocked (${params.brain_coins} brain ${Math.abs(params.brain_coins) > 1?'coins':'coin'})`
            break
          case 'P':
            message = `You have purchase ${params.brain_coins} brain coins!`
            break
          case 'B':
            message = `You have received ${params.brain_coins} brain ${Math.abs(params.brain_coins) > 1?'coins':'coin'}!!`
            break
          default:
            message = `You have made a new transaction!`
            break
        }
        toast({
          title: params.description ?? message,
          variant: 'info'
        })
        storeAuth.user.brain_coins += transaction.brain_coins
        return transaction
      }
    } catch (e) {
      storeError.setErrorMessages(e.response.data.message, e.response.data.errors, e.response.status, 'Error creating game!')
      return false
    }
  }

  return {
    transactions,
    totalTransactions,
    page,
    filters,
    resetPage,
    fetchTransactions,
    fetchTransactionsNextPage,
    createTransaction,
  }
})
