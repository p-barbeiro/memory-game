<template>
  <div class="place-self-center flex flex-col h-full justify-center">
    <h1 class="text-4xl xl:text-4xl font-black md:mb-10 mb-5">Select your game board and dive into the challenge!</h1>
    <div class="flex flex-col md:flex-row w-full gap-3 justify-center">
      <BoardCard v-for="board in storeBoards.boards" :board="board" />
    </div>
  </div>
</template>

<script setup>
import { useBoardStore } from '@/stores/board'
import BoardCard from './BoardCard.vue'

const storeBoards = useBoardStore()
</script>
