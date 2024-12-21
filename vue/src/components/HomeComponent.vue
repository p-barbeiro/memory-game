<template>
  <div class="place-self-center flex flex-col md:flex-row justify-between items-center h-full w-full">
    <div>
      <h1 class="text-6xl xl:text-7xl text-center font-black">Memory Game</h1>
      <div class="lg:text-lg text-center pb-10 md:pb-0 pt-10 xl:pt-10">
        <h2>Unleash your mind's potential our with memory game!</h2>
        <h2><b>Challenge your focus</b>, <b>boost your memory</b>, and enjoy endless fun.</h2>
      </div>
      <RouterLink v-if="!storeAuth.isAdmin" :to="{ name: 'gamemode' }">
        <Button class="w-full text-xl xl:mt-8 py-6 mb-5">Let's Play</Button>
      </RouterLink>
    </div>
    <div class="h-full md:w-1/2 md:ml-8">
      <img class="md:h-full rounded-xl object-cover" src="/src/assets/img/memory_game.jpg" alt="" />
    </div>
  </div>
</template>

<script setup>
import { useAuthStore } from '@/stores/auth';
import Button from './ui/button/Button.vue'

const storeAuth = useAuthStore()
</script>
