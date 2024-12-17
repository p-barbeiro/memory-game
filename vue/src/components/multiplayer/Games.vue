<script setup>
import { onMounted } from 'vue'
import Game from './Game.vue';
import { useGameStore } from '@/stores/game';
import { useMultiplayerStore } from '@/stores/multiplayer';

const multiplayerStore = useMultiplayerStore()

onMounted(() => {
    multiplayerStore.fetchPlayingGames()
})
</script>

<template>
    <div class="flex flex-wrap justify-stretch my-5">
        <Game v-for="game in multiplayerStore.games" :key="game.id" :game="game" class="border p-5 rounded-lg shadow">
        </Game>
    </div>
</template>