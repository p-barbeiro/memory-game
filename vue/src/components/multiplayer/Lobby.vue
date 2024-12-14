<script setup>
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { Button } from '@/components/ui/button'
import { onMounted } from 'vue'
import GamesLobby from './GamesLobby.vue'
import { useLobbyStore } from '@/stores/lobby'
import Chat from '../chat/Chat.vue'
import Modal from '../ui/modal/Modal.vue'
import { ref } from 'vue'

const storeLobby = useLobbyStore()

const showModal = ref(false)

const addGame = (board) => {
  storeLobby.addGame(board)
  showModal.value = false
}

onMounted(() => {
  storeLobby.fetchGames()
})
</script>

<template>
  <Modal v-model:show="showModal" @close="showModal = false">
    <template #header>
      <h3 class="text-2xl font-semibold text-center text-indigo-950">Choose your game board</h3>
    </template>
    <template #body>
      <p class="text-center text-gray-600">
        Select the game board you want to play with. <br />Every board has a cost of <b>5 Brain coins</b>.<br />
        The winner will get 7 Brain coins.
      </p>
    </template>
    <template #footer>
      <div class="flex flex-row gap-5 justify-center w-full">
        <Button class="w-1/4 h-10 rounded-f shadow font-semibold text-lg" @click='addGame({id:1,desc:"3 x 4"})'>3 x 4</Button>
        <Button class="w-1/4 h-10 rounded-f shadow font-semibold text-lg" @click='addGame({id:2,desc:"4 x 4"})'>4 x 4</Button>
        <Button class="w-1/4 h-10 rounded-f shadow font-semibold text-lg" @click='addGame({id:3,desc:"6 x 6"})'>6 x 6</Button>
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
          <Button @click="showModal = true"> New Game </Button>
        </div>
      </CardHeader>
      <CardContent class="p-4">
        <div v-if="storeLobby.totalGames > 0">
          <GamesLobby></GamesLobby>
        </div>
        <div v-else>
          <h2 class="text-xl text-center py-5">No games available!</h2>
        </div>
      </CardContent>
    </Card>
  </div>
</template>
