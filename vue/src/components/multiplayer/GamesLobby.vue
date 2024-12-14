<script setup>
import { useAuthStore } from '@/stores/auth'
import { useLobbyStore } from '@/stores/lobby'
import Card from '../ui/card/Card.vue'
import Button from '../ui/button/Button.vue';

const storeAuth = useAuthStore()
const storeLobby = useLobbyStore()
</script>

<template>
  <div class="grid md:grid-cols-4 gap-3">
    <div v-for="game in storeLobby.games" :key="game.id">
      <Card class="w-full">
        <div class="flex flex-row items-center p-2 border-b">
          <img class="w-10 h-10 rounded-full" :src="storeAuth.getPhotoURL(game.player1.photoFileName)" alt="avatar" />
          <div class="ml-2">
            <p class="text-sm font-semibold text-gray-800">{{ game.player1.nickname }}</p>
            <p class="text-xs text-gray-600">{{ new Date(game.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }) }}</p>
          </div>
        </div>
        <div class="p-1 text-sm">
            <p class="text-gray-800 text-center text-lg font-bold">{{ game.board.desc }}</p>
        </div>
        <div class="px-1 pb-1">
            <Button variant="secondary" v-show="storeLobby.canJoinGame(game)" @click="storeLobby.joinGame(game.id)" class="w-full bg-indigo-200/50 hover:bg-indigo-400/10 border border-indigo-200">Join Game</Button>
            <Button variant="destructive" v-show="storeLobby.canRemoveGame(game)" @click="storeLobby.removeGame(game.id)" class="w-full bg-red-200/50 text-red-700 border-red-200 border hover:bg-red-700/20">Remove</Button>
        </div>
      </Card>
    </div>
  </div>
</template>
