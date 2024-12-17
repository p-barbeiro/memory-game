<script setup>
import { Button } from '@/components/ui/button'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { useAuthStore } from '@/stores/auth'
import { useLobbyStore } from '@/stores/lobby'
import { computed, onMounted, ref } from 'vue'
import { onBeforeRouteLeave } from 'vue-router'
import Chat from '../chat/Chat.vue'
import Modal from '../ui/modal/Modal.vue'
import Games from './Games.vue'
import GamesLobby from './GamesLobby.vue'
import { inject } from 'vue'
import { useMultiplayerStore } from '@/stores/multiplayer'
import { useTransactionStore } from '@/stores/transaction'
import { useToast } from '@/components/ui/toast'

const { toast } = useToast()
const storeAuth = useAuthStore()
const storeLobby = useLobbyStore()
const storeMultiplayer = useMultiplayerStore()
const alertDialog = inject('alertDialog')
const showModal = ref(false)
const storeTransaction = useTransactionStore()

const addGame = async (board) => {
  if (storeAuth.user.brain_coins < 5) {
    toast({
      title: `Not enough Brain Coins`,
      description: `You need at least 5 Brain Coins to create a game.`,
      variant: 'destructive'
    })
    showModal.value = false

    return
  }
  const gameID = await storeLobby.addGame(board)
  console.log('Game created with ID:', gameID)
  storeTransaction.newTransaction({
    type: 'I',
    brain_coins: -5,
    description: `Multiplayer Game Created! -5 Brain Coins`,
    game_id: gameID
  })
  showModal.value = false
}

onMounted(() => {
  storeLobby.fetchGames()
})

const gamesCreated = computed(() => {
  return storeLobby.games.filter((game) => game.player1.id === storeAuth.user.id) ?? []
})

onBeforeRouteLeave((to, from, next) => {
  if ((storeMultiplayer.totalGames > 0 && storeMultiplayer.games.forEach((game) => game.gameStatus > 0)) || gamesCreated.value.length > 0) {
    let message = ''
    if (storeMultiplayer.totalGames > 0 && storeMultiplayer.games.forEach((game) => game.gameStatus > 0)) {
      message += `You have <b>${storeMultiplayer.totalGames}</b> game(s) in progress.<br>`
    }
    if (gamesCreated.value.length > 0) {
      message += `You have <b>${gamesCreated.value.length}</b> game(s) created.<br>`
    }
    message += ' Are you sure you want to leave? All progress will be lost.'

    showConfirmationDialog('Exit confirmation?', message, 'Cancel', 'Leave').then((confirmed) => {
      if (confirmed) {
        for (let i = 0; i < gamesCreated.value.length; i++) {
          storeLobby.removeGame(gamesCreated.value[i].id)
        }
        next() // Proceed to the next route
      } else {
        next(false) // Stay on the current route
      }
    })
  } else {
    next() // No confirmation needed, proceed
  }
})

function showConfirmationDialog(title, description, cancelLabel, actionLabel) {
  return new Promise((resolve) => {
    alertDialog.value.open(
      () => resolve(true), // Action callback
      title,
      cancelLabel,
      actionLabel,
      description,
      () => resolve(false) // Cancel callback
    )
  })
}
</script>

<template>
  <Modal v-model:show="showModal" @close="showModal = false" >
    <template #header>
      <h3 class="font-semibold text-2xl text-center text-indigo-950">Choose your game board</h3>
    </template>
    <template #body>
      <p class="text-center text-gray-600">
        Select the game board you want to play with. <br />Every board has a cost of <b>5 Brain coins</b>.<br />
        The winner will get 7 Brain coins.
      </p>
    </template>
    <template #footer>
      <div class="flex flex-row justify-center gap-5 w-full">
        <Button class="shadow rounded-f w-1/4 h-10 font-semibold text-lg" @click="addGame({ id: 1, cols: 3, rows: 4, desc: '3 x 4' })">3 x 4</Button>
        <Button class="shadow rounded-f w-1/4 h-10 font-semibold text-lg" @click="addGame({ id: 2, cols: 4, rows: 4, desc: '4 x 4' })">4 x 4</Button>
        <Button class="shadow rounded-f w-1/4 h-10 font-semibold text-lg" @click="addGame({ id: 3, cols: 6, rows: 6, desc: '6 x 6' })">6 x 6</Button>
      </div>
    </template>
  </Modal>
  <div class="w-full">
    <Chat class="mb-4"></Chat>
    <Card>
      <CardHeader class="flex flex-row justify-between pb-0">
        <div>
          <CardTitle class="mb-3">Lobby</CardTitle>
          <CardDescription>{{ storeLobby.totalGames == 1 ? '1 game' : storeLobby.totalGames + ' games' }} waiting...</CardDescription>
        </div>
        <div>
          <Button @click="showModal = true" @keydown.esc="showModal = false"> New Game </Button>
        </div>
      </CardHeader>
      <CardContent class="p-4">
        <div v-if="storeLobby.totalGames > 0">
          <GamesLobby></GamesLobby>
        </div>
        <div v-else>
          <h2 class="py-5 text-center text-xl">No games available!</h2>
        </div>
      </CardContent>
    </Card>
    <Games class="flex-1 gap-5"></Games>
  </div>
</template>
