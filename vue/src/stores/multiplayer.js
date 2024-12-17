import { useToast } from '@/components/ui/toast/use-toast'
import { useErrorStore } from '@/stores/error'
import { defineStore } from 'pinia'
import { computed, inject, ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from './auth'
import { useGameStore } from './game'
import axios from 'axios'
import { useTransactionStore } from './transaction'

export const useMultiplayerStore = defineStore('multiplayer', () => {
  const gameStore = useGameStore()
  const storeError = useErrorStore()
  const storeTransaction = useTransactionStore()
  const { toast } = useToast()
  const auth = useAuthStore()
  const router = useRouter()
  const socket = inject('socket')

  const games = ref([])

  const totalGames = computed(() => games.value.length)

  socket.on('gameStart', async (game) => {
    console.log('Game started: ', game)
    if (game.created_user_id.id === auth.userID) {
      toast({
        title: 'Game Started',
        description: `Game #${game.id} has started!`
      })
    }
    gameStore.updateGame(game.id, { status: 'PL' })
    fetchPlayingGames()
  })

  // fetch playing games from the Websocket server
  const fetchPlayingGames = () => {
    storeError.resetMessages()
    socket.emit('fetchPlayingGames', (response) => {
      if (webSocketServerResponseHasError(response)) {
        return
      }
      games.value = response
    })
  }

  const webSocketServerResponseHasError = (response) => {
    if (response.errorCode) {
      storeError.setErrorMessages(response.errorMessage, [], response.errorCode)
      return true
    }
    return false
  }

  const playerNumberOfCurrentUser = (game) => {
    if (game.player1.id === auth.userID) {
      return 1
    }
    if (game.player2.id === auth.userID) {
      return 2
    }
    return null
  }

  const switchPlayer = (game) => {
    storeError.resetMessages()
    socket.emit('switchPlayer', game.id, (response) => {
      if (webSocketServerResponseHasError(response)) {
        return
      }
    })
  }

  const updateCard = (game, card) => {
    storeError.resetMessages()
    socket.emit(
      'play',
      {
        card: card,
        gameId: game.id
      },
      (response) => {
        if (webSocketServerResponseHasError(response)) {
          return
        }
        updateGame(response)
      }
    )
  }

  const updateGame = (game) => {
    const gameIndex = games.value.findIndex((g) => g.id === game.id)
    if (gameIndex !== -1) {
      games.value[gameIndex] = { ...game } // shallow copy
    }
  }

  socket.on('gameChanged', (game) => {
    //console.log('Game changed: ', game)
    updateGame(game)
  })

  socket.on('gameEnded', async (game) => {
    updateGame(game)
    console.log('Game ended: ', game)
    // Player that created the game is responsible for updating on the database
    const winner = game.gameStatus === 1 ? game.player1.id : game.player2.id

    if (playerNumberOfCurrentUser(game) === 1) {
      const APIresponse = await gameStore.updateGame(game.id, { status: 'E', winner_user_id: winner })
      const updatedGameOnDB = APIresponse
      console.log('Game has ended and updated on the database: ', updatedGameOnDB)
    }

    // If the game is ended and the player is the winner, add 7 Brain Coins
    if (game.gameStatus === 1 || game.gameStatus === 2) {
      if (winner === auth.userID) {
        storeTransaction.newTransaction({
          type: 'B',
          brain_coins: 7,
          description: `Won game #${game.id}! +7 Brain Coins`
        })
      }
    }

    //Update multiplayer game status
    const multiplayerID = game.player1.id === auth.userID ? game.player1_mp_id : game.player2_mp_id
    const body = {
      player_won: winner === auth.userID ? 1 : 0,
      pairs_discovered: game.player1.id === auth.userID ? game.player1_pairs : game.player2_pairs
    }
    const mpResponse = await axios.patch('games/multiplayer/' + multiplayerID, body)
    console.log('Multiplayer game updated: ', mpResponse.data.data)
  })

  const close = (game) => {
    storeError.resetMessages()
    socket.emit('closeGame', game.id, (response) => {
      if (webSocketServerResponseHasError(response)) {
        return
      }
      removeGameFromList(game)
    })
  }

  const removeGameFromList = (game) => {
    const gameIndex = games.value.findIndex((g) => g.id === game.id)
    if (gameIndex >= 0) {
      games.value.splice(gameIndex, 1)
    }
  }

  const quit = (game) => {
    storeError.resetMessages()
    socket.emit('quitGame', game.id, (response) => {
      if (webSocketServerResponseHasError(response)) {
        return
      }
      removeGameFromList(game)
    })
  }

  socket.on('gameQuitted', async (payload) => {
    if (payload.userQuit.id != auth.userID) {
      toast({
        title: 'Game Quit',
        description: `${payload.userQuit.name} has quitted game #${payload.game.id}, giving you the win!`
      })
    }
    updateGame(payload.game)
  })

  socket.on('gameInterrupted', async (game) => {
    updateGame(game)
    toast({
      title: 'Game Interruption',
      description: `Game #${game.id} was interrupted because your opponent has gone offline!`,
      variant: 'destructive'
    })
    const APIresponse = await axios.patch('games/' + game.id, {
      status: 'I',
      winner_user_id: game.gameStatus === 1 ? game.player1_id : game.gameStatus === 2 ? game.player2_id : null
    })
    const updatedGameOnDB = APIresponse.data.data
    console.log('Game was interrupted and updated on the database: ', updatedGameOnDB)
  })

  return {
    games,
    totalGames,
    fetchPlayingGames,
    playerNumberOfCurrentUser,
    updateCard,
    switchPlayer,
    close,
    quit
  }
})
