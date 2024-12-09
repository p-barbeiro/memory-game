<template>
  <div class="w-full p-8 h-[calc(100%-6rem)] md:rounded-xl md:shadow bg-white">
    <div class="flex flex-col h-full justify-center">
      <h1 class="text-4xl xl:text-3xl font-black md:mb-10 mb-5">Go head-to-head with friends or master the game on your own. The choice is yours!</h1>
      <div class="flex flex-col md:flex-row gap-5 justify-center h-full">
        <GameModeCard gamemode="Single-Player" desc="Challenge yourself and sharpen your memory in solo mode—just you and the cards!" img="single_player"/>
        <GameModeCard gamemode="Multiplayer" desc="Compete with friends and show off your memory skills in multiplayer mode!" img="multiplayer"/>
      </div>
    </div>
  </div>
</template>

<script setup>
import GameModeCard from './GameModeCard.vue';

</script>
