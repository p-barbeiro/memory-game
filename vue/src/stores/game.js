import { useToast } from '@/components/ui/toast/use-toast'
import { useErrorStore } from '@/stores/error'
import axios from 'axios'
import { defineStore } from 'pinia'
import { computed, ref } from 'vue'
import { useAuthStore } from './auth'

export const useGameStore = defineStore('game', () => {
  const storeError = useErrorStore()
  const { toast } = useToast()
  const auth = useAuthStore()

  const games = ref([])
  const gamesWithPagination = ref([])
  const totalGames = computed(() => games.value.length)

  const fetchGames = async () => {
    storeError.resetMessages()
    try {
      axios.defaults.headers.common.Authorization = 'Bearer ' + auth.token
      axios.defaults.headers.common['Content-Type'] = 'application/json'
      const response = await axios.get(`users/${auth.userID}/games?&qnt=15`)
      games.value = response.data.data
      gamesWithPagination.value = response.data
    } catch (e) {
      storeError.setErrorMessages(e.response.data.message, e.response.data.errors, e.response.status, 'Error fetching games!')
      return false
    }
  }

  const getBoardIndex = (id) => {
    return boards.value.findIndex((p) => p.id === id)
  }

  const getBoard = (id) => {
    return boards.value.find((p) => p.id === id)
  }

  const fetchBoard = async (boardID) => {
    storeError.resetMessages()
    const response = await axios.get('boards/' + boardID)
    const index = getBoardIndex(boardID)
    if (index > -1) {
      // Instead of a direct assignment, object is cloned/copied to the array
      // This ensures that the object in the array is not the same as the object fetched
      boards.value[index] = Object.assign({}, response.data.data)
    }
    return response.data.data
  }

  return {
    games,
    gamesWithPagination,
    totalGames,
    fetchGames,
  }
})
