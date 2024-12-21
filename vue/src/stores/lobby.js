import { useAuthStore } from '@/stores/auth'
import { useErrorStore } from '@/stores/error'
import axios from 'axios'
import { defineStore } from 'pinia'
import { computed, inject, ref } from 'vue'
import { useGameStore } from './game'
import { useTransactionStore } from './transaction'
import { useToast } from '@/components/ui/toast'

export const useLobbyStore = defineStore('lobby', () => {
  const storeAuth = useAuthStore()
  const storeError = useErrorStore()
  const storeGame = useGameStore()
  const socket = inject('socket')
  const storeTransaction = useTransactionStore()
  const { toast } = useToast()

  const games = ref([])
  const totalGames = computed(() => games.value.length)

  const webSocketServerResponseHasError = (response) => {
    if (response.errorCode) {
      storeError.setErrorMessages(response.errorMessage, [], response.errorCode)
      return true
    }
    return false
  }

  // when the lobby changes on the server, it is updated on the client
  socket.on('lobbyChanged', (lobbyGames) => {
    games.value = lobbyGames
  })

  // fetch lobby games from the Websocket server
  const fetchGames = () => {
    storeError.resetMessages()
    socket.emit('fetchGames', (response) => {
      if (webSocketServerResponseHasError(response)) {
        return
      }
      games.value = response
    })
  }

  // add a game to the lobby
  const addGame = async (board) => {
    storeError.resetMessages()
    axios.defaults.headers.common.Authorization = 'Bearer ' + storeAuth.token
    axios.defaults.headers.common['Content-Type'] = 'application/json'
    const gameAPI = await storeGame.newGame(board.id, 'M', storeAuth.userID)
    socket.emit('addGame', board, gameAPI.id, (response) => {
      if (webSocketServerResponseHasError(response)) {
        return
      }
      console.log('Game added', response)
    })
    return gameAPI.id
  }

  // remove a game from the lobby
  const removeGame = async (id) => {
    storeError.resetMessages()
    const game = games.value.find((game) => game.id === id)
    const gameRemoved = await storeGame.cancelGame(game.game_id)
    console.log('Game removed', gameRemoved)
    socket.emit('removeGame', id, (response) => {
      if (webSocketServerResponseHasError(response)) {
        return
      }
    })
  }

  // join a game of the lobby
  const joinGame = (id) => {
    storeError.resetMessages()
    //check if the user has enough brain coins
    if (storeAuth.user.brain_coins < 5) {
      toast({
        title: `Not enough Brain Coins`,
        description: `You need at least 5 Brain Coins to join a game.`,
        variant: 'destructive'
      })
      return
    }
    socket.emit('joinGame', id, async (response) => {
      // callback executed after the join is complete
      if (webSocketServerResponseHasError(response)) {
        return
      }

      const gameWS = response
      console.log('Game joined', gameWS)

      const gameAPI = await storeGame.fetchGame(gameWS.game_id)
      const newGameOnDB = gameAPI
      storeTransaction.createTransaction({
        type: 'I',
        brain_coins: -5,
        description: `Joined game #${gameWS.game_id}! -5 Brain Coins`,
        game_id: gameAPI.id
      })
      for (let player = 1; player <= gameWS.total_players; player++) {
        const body = {
          game_id: gameWS.game_id,
          user_id: gameWS[`player${player}`].id
        }
        const mpAPI = await axios.post('games/multiplayer', body)
        const newGameMP = mpAPI.data.data
        console.log('MP created', newGameMP)
        newGameOnDB[`player${player}`] = gameWS[`player${player}`]
        newGameOnDB[`player${player}_pairs`] = 0
        newGameOnDB[`player${player}_mp_id`] = newGameMP.id
        newGameOnDB[`player${player}_turns`] = 0
        newGameOnDB[`player${player}SocketId`] = response[`player${player}SocketId`]
      }1

      // fill deck
      const total_pairs = (gameAPI.board.rows * gameAPI.board.columns) / 2
      let cards = []
      for (let i = 1; i <= total_pairs; i++) {
        cards.push({ id: i, flipped: false, matched: false })
        cards.push({ id: i, flipped: false, matched: false })
      }
      cards = cards.sort(() => Math.random() - 0.5)
      cards.forEach((card, index) => {
        card.uniqueID = index
      })
      newGameOnDB.cards = cards
      storeGame.startGame(newGameOnDB.id)
      socket.emit('startGame', newGameOnDB, (startedGame) => {
        console.log('Game has started', startedGame)
      })
    })
  }

  // Whether the current user can remove a specific game from the lobby
  const canRemoveGame = (game) => {
    return game.player1.id === storeAuth.userID
  }

  // Whether the current user can join a specific game from the lobby
  const canJoinGame = (game) => {
    return storeAuth.user && game.player1.id !== storeAuth.userID
  }

  return {
    games,
    totalGames,
    fetchGames,
    addGame,
    joinGame,
    canJoinGame,
    removeGame,
    canRemoveGame
  }
})
