<script setup>
import { useAuthStore } from '@/stores/auth'
import { useLobbyStore } from '@/stores/lobby'
import Card from '../ui/card/Card.vue'
import Button from '../ui/button/Button.vue'
import { inject } from 'vue'

const storeAuth = useAuthStore()
const storeLobby = useLobbyStore()

const alertDialog = inject('alertDialog')

const deleteGame = (gameID) => {
  alertDialog.value.open(() => removeConfirm(gameID), 'Remove confirmation', 'Cancel', 'Yes, I want to remove', 'Are you sure you want to remove this game? <br>Brain coins will not be refunded.')
}

const removeConfirm = (gameID) => {
  console.log('Game removed')
  storeLobby.removeGame(gameID)
}
</script>

<template>
  <div class="gap-3 grid md:grid-cols-4">
    <div v-for="game in storeLobby.games" :key="game.id">
      <Card class="w-full">
        <div class="flex flex-row justify-between items-center p-2 border-b">
          <div class="flex flex-row items-center">
            <img class="rounded-full w-10 h-10" :src="storeAuth.getPhotoURL(game.player1.photoFileName)" alt="avatar" />
            <div class="ml-2">
              <p class="font-semibold text-gray-800 text-sm">{{ game.player1.nickname }}</p>
              <p class="text-gray-600 text-xs">{{ new Date(game.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }) }}</p>
            </div>
          </div>
          <div class="flex flex-row items-center text-sm">Players: {{ game.players }}/{{ game.total_players }}</div>
        </div>
        <div class="p-1 text-sm">
          <p class="font-black text-center text-gray-800 text-lg">{{ game.board.desc }}</p>
        </div>
        <div class="px-1 pb-1">
          <Button variant="secondary" v-show="storeLobby.canJoinGame(game)" @click="storeLobby.joinGame(game.id)" class="border-indigo-200 bg-indigo-200/50 hover:bg-indigo-400/10 border w-full">Join Game</Button>
          <Button variant="destructive" v-show="storeLobby.canRemoveGame(game)" @click="deleteGame(game.id)" class="bg-red-200/50 hover:bg-red-700/20 border border-red-200 w-full text-red-700">Remove</Button>
        </div>
      </Card>
    </div>
  </div>
</template>
