import { useToast } from '@/components/ui/toast/use-toast'
import { useErrorStore } from '@/stores/error'
import axios from 'axios'
import { defineStore } from 'pinia'
import { computed, ref, inject } from 'vue'
import { useAuthStore } from './auth'
import { useRouter } from 'vue-router'

export const useGameStore = defineStore('game', () => {
  const storeAuth = useAuthStore()
  const storeError = useErrorStore()
  const router = useRouter()

  const { toast } = useToast()

  const games = ref([])
  const totalGames = ref(0)
  const page = ref(1)
  const filters = ref({
    type: '',
    status: '',
    board: '',
    sort_by: 'created_at',
    sort_direction: 'desc'
  })

  const resetPage = () => {
    page.value = 1
    games.value = null
  }

  const fetchGames = async (resetPagination = false) => {
    storeError.resetMessages()
    try {
      if (resetPagination) {
        resetPage()
      }

      const queryParams = new URLSearchParams({
        page: page.value,
        ...(filters.value.type && { type: filters.value.type }),
        ...(filters.value.status && { status: filters.value.status }),
        ...(filters.value.board && { board: filters.value.board }),
        sort_by: filters.value.sort_by,
        sort_direction: filters.value.sort_direction
      }).toString()

      axios.defaults.headers.common.Authorization = 'Bearer ' + storeAuth.token
      axios.defaults.headers.common['Content-Type'] = 'application/json'

      const response = await axios.get(`games?${queryParams}`)

      if (response.status !== 200) {
        throw response
      }
      totalGames.value = response.data.meta.total

      if (page.value === 1 || resetPagination) {
        games.value = response.data.data
      } else {
        games.value = [...(games.value || []), ...response.data.data]
      }

      return response.data
    } catch (e) {
      storeError.setErrorMessages(e.response.data.message, e.response.data.errors, e.response.status, 'Fetch Users Error')
      return false
    }
  }

  const fetchGamesNextPage = async () => {
    page.value++
    await fetchGames()
  }

  // const fetchGames = async (page = '', order_by = '', game_type = '', board = '', game_status = '') => {
  //   storeError.resetMessages()
  //   try {
  //     axios.defaults.headers.common.Authorization = 'Bearer ' + auth.token
  //     axios.defaults.headers.common['Content-Type'] = 'application/json'
  //     var url = `users/${auth.userID}/games?`
  //     if (order_by) {
  //       url += `&order_by=${order_by}`
  //     }
  //     if (game_type) {
  //       url += `&game_type=${game_type}`
  //     }
  //     if (page) {
  //       url += `&page=${page}`
  //     }
  //     if (board) {
  //       url += `&board=${board}`
  //     }
  //     if (game_status) {
  //       url += `&game_status=${game_status}`
  //     }
  //     console.log(url)

  //     const response = await axios.get(url)

  //     games.value = response.data.data
  //     meta.value = response.data.meta
  //     links.value = response.data.links
  //   } catch (e) {
  //     storeError.setErrorMessages(e.response.data.message, e.response.data.errors, e.response.status, 'Error fetching games!')
  //     return false
  //   }
  // }

  const newGame = async (boardID, game_type = 'S', creator = undefined) => {
    storeError.resetMessages()
    try {
      axios.defaults.headers.common.Authorization = 'Bearer ' + storeAuth.token
      axios.defaults.headers.common['Content-Type'] = 'application/json'

      const body = {
        board_id: boardID,
        type: game_type
      }

      if (creator) {
        body.created_user_id = creator
      }
      const response = await axios.post(`games`, body)
      console.log(response)

      if (response.status === 201) {
        const game = response.data.data
        console.log(game)
        if (game_type === 'S') {
          toast({
            title: 'New game created!',
            variant: 'success'
          })
          router.push({ name: 'game', params: { gameid: game.id } })
        }
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
      axios.defaults.headers.common.Authorization = 'Bearer ' + storeAuth.token
      axios.defaults.headers.common['Content-Type'] = 'application/json'

      const body = {
        ...params
      }

      const response = await axios.patch(`games/${gameID}`, body)
      console.log('UPDATE', response)
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
      axios.defaults.headers.common.Authorization = 'Bearer ' + storeAuth.token
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
      axios.defaults.headers.common.Authorization = 'Bearer ' + storeAuth.token
      axios.defaults.headers.common['Content-Type'] = 'application/json'

      const url = `games/${gameID}/cancel`
      console.log(url)

      const response = await axios.post(url)

      if (response.status === 200) {
        console.log('game canceled')
        if (router.currentRoute.name === 'game' && router.currentRoute.params.gameid === gameID) router.push({ name: 'boardSelector' })
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
      axios.defaults.headers.common.Authorization = 'Bearer ' + storeAuth.token
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
    totalGames,
    page,
    filters,
    fetchGames,
    fetchGamesNextPage,
    newGame,
    updateGame,
    startGame,
    cancelGame,
    fetchGame
  }
})
