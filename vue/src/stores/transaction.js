import { useToast } from '@/components/ui/toast/use-toast'
import { useErrorStore } from '@/stores/error'
import axios from 'axios'
import { defineStore } from 'pinia'
import { computed, ref } from 'vue'
import { useAuthStore } from './auth'
import { useRouter } from 'vue-router'

export const useTransactionStore = defineStore('transaction', () => {
  const storeError = useErrorStore()
  const { toast } = useToast()
  const auth = useAuthStore()
  const router = useRouter()

  const transactions = ref([])
  const meta = ref([])
  const links = ref([])

  const totalTransactions = computed(() => games.value.length)

  const fetchTransactions = async (page = '', order_by = '', type = '') => {
    storeError.resetMessages()
    try {
      axios.defaults.headers.common.Authorization = 'Bearer ' + auth.token
      axios.defaults.headers.common['Content-Type'] = 'application/json'
      
      var url = `users/${auth.userID}/transactions?`
      
      if (order_by) {
        url += `&order_by=${order_by}`
      }
      if (page) {
        url += `&page=${page}`
      }
      if (type) {
        url += `&type=${type}`
      }

      console.log(url);
      
      const response = await axios.get(url)

      transactions.value = response.data.data
      meta.value = response.data.meta
      links.value = response.data.links
    } catch (e) {
      storeError.setErrorMessages(e.response.data.message, e.response.data.errors, e.response.status, 'Error fetching transactions!')
      return false
    }
  }

  const newTransaction = async (params) => {
    storeError.resetMessages()
    try {
      axios.defaults.headers.common.Authorization = 'Bearer ' + auth.token
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
        auth.user.brain_coins += transaction.brain_coins
        return transaction
      }
    } catch (e) {
      storeError.setErrorMessages(e.response.data.message, e.response.data.errors, e.response.status, 'Error creating game!')
      return false
    }
  }

  const updateGame = async (gameID, params) => {
    storeError.resetMessages()
    try {
      var game = null
      axios.defaults.headers.common.Authorization = 'Bearer ' + auth.token
      axios.defaults.headers.common['Content-Type'] = 'application/json'

      const body = {
        ...params
      }

      const response = await axios.patch(`games/${gameID}`, body)

      if (response.status === 200) {
        console.log(game)
        return response.data.data
      }
    } catch (e) {
      storeError.setErrorMessages(e.response.data.message, e.response.data.errors, e.response.status, 'Error creating game!')
      return false
    }
  }

  const startGame = async (gameID) => {
    storeError.resetMessages()
    try {
      axios.defaults.headers.common.Authorization = 'Bearer ' + auth.token
      axios.defaults.headers.common['Content-Type'] = 'application/json'

      const url = `games/${gameID}/start`
      console.log(url)

      const response = await axios.post(url)

      if (response.status === 200) {
        console.log('game started')
        return response.data.data
      }
    } catch (e) {
      storeError.setErrorMessages(e.response.data.message, e.response.data.errors, e.response.status, 'Error starting game!')
      return false
    }
  }

  const cancelGame = async (gameID) => {
    storeError.resetMessages()
    try {
      axios.defaults.headers.common.Authorization = 'Bearer ' + auth.token
      axios.defaults.headers.common['Content-Type'] = 'application/json'

      const url = `games/${gameID}/cancel`
      console.log(url)

      const response = await axios.post(url)

      if (response.status === 200) {
        console.log('game canceled')
        return response.data.data
      }
    } catch (e) {
      storeError.setErrorMessages(e.response.data.message, e.response.data.errors, e.response.status, 'Error starting game!')
      return false
    }
  }

  return {
    transactions,
    meta,
    links,
    totalTransactions,
    fetchTransactions,
    newTransaction,
    updateGame,
    startGame
  }
})
