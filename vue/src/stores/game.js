import { useToast } from '@/components/ui/toast/use-toast'
import { useErrorStore } from '@/stores/error'
import axios from 'axios'
import { defineStore } from 'pinia'
import { computed, ref } from 'vue'
import { useAuthStore } from './auth'
import { useRouter } from 'vue-router'

export const useGameStore = defineStore('game', () => {
  const storeError = useErrorStore()
  const { toast } = useToast()
  const auth = useAuthStore()
  const router = useRouter()

  const games = ref([])
  const meta = ref([])
  const links = ref([])

  const totalGames = computed(() => games.value.length)

  const fetchGames = async (page = '', order_by = '', game_type = '', board = '', game_status = '') => {
    storeError.resetMessages()
    try {
      axios.defaults.headers.common.Authorization = 'Bearer ' + auth.token
      axios.defaults.headers.common['Content-Type'] = 'application/json'
      var url = `users/${auth.userID}/games?`
      if (order_by) {
        url += `&order_by=${order_by}`
      }
      if (game_type) {
        url += `&game_type=${game_type}`
      }
      if (page) {
        url += `&page=${page}`
      }
      if (board) {
        url += `&board=${board}`
      }
      if (game_status) {
        url += `&game_status=${game_status}`
      }
      console.log(url)

      const response = await axios.get(url)

      games.value = response.data.data
      meta.value = response.data.meta
      links.value = response.data.links
    } catch (e) {
      storeError.setErrorMessages(e.response.data.message, e.response.data.errors, e.response.status, 'Error fetching games!')
      return false
    }
  }

  const newGame = async (boardID, game_type = 'S') => {
    storeError.resetMessages()
    try {
      axios.defaults.headers.common.Authorization = 'Bearer ' + auth.token
      axios.defaults.headers.common['Content-Type'] = 'application/json'

      const body = {
        board_id: boardID,
        type: game_type
      }

      const response = await axios.post(`games`, body)
      console.log(response)

      if (response.status === 201) {
        const game = response.data.data
        console.log(game)
        toast({
          title: 'New game created!',
          variant: 'success'
        })
        router.push({ name: 'game', params: { gameid: game.id } })
        return game
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
        router.push({ name: 'boardSelector' })
        return response.data.data
      }
    } catch (e) {
      storeError.setErrorMessages(e.response.data.message, e.response.data.errors, e.response.status, 'Error starting game!')
      return false
    }
  }

  const getGameIndex = (id) => {
    return games.value.findIndex((p) => p.id === id)
  }

  const fetchGame = async (gameID) => {
    storeError.resetMessages()
    try {
        axios.defaults.headers.common.Authorization = 'Bearer ' + auth.token
      axios.defaults.headers.common['Content-Type'] = 'application/json'

      const response = await axios.get('games/' + gameID)
      const index = getGameIndex(gameID)
      if (index > -1) {
        games.value[index] = Object.assign({}, response.data.data)
      }
      return response.data.data
    } catch (e) {
      storeError.setErrorMessages(e.response.data.message, e.response.data.errors, e.response.status, 'Error fetching game!')
      return false
    }
  }

  return {
    games,
    meta,
    links,
    totalGames,
    fetchGames,
    newGame,
    updateGame,
    startGame,
    cancelGame,
    fetchGame
  }
})
