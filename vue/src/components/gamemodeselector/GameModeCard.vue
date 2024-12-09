<template>
  <div class="md:w-1/2 h-full">
    <div class="py-5 px-4 md:px-0 md:py-0 bg-gray-100 border border-indigo-100 shadow rounded-xl text-left h-full overflow-hidden flex flex-col justify-between">
      <div class="h-2/3">
        <img :class="{ 'blur-md select-none': guest_multiplayer }" :src="link" alt="Game Mode" class="w-full h-full object-cover rounded md:rounded-b-none md:rounded-t-xl" />
      </div>
      <div :class="{ 'blur select-none': guest_multiplayer }" class="md:py-5 md:px-4">
        <div :class="{ 'blur select-none': guest_multiplayer }" class="text-lg text-center pt-5">{{ desc }}</div>
        <RouterLink :to="{ name: 'boardSelector' }">
          <Button v-if="!guest_multiplayer" class="w-full text-2xl h-auto py-6 mt-5">{{ gamemode }}</Button>
        </RouterLink>
      </div>
      <div v-if="guest_multiplayer" class="text-lg text-center"><b>Create</b> an account or <b>login</b> to use multiplayer mode.</div>
      <div v-if="guest_multiplayer" class="md:pb-5 md:px-4 flex flex-row gap-5">
        <RouterLink :to="{ name: 'register' }" class="w-full">
          <Button class="w-full text-base h-auto py-6 mt-5" variant="outline">Register</Button>
        </RouterLink>
        <RouterLink :to="{ name: 'login' }" class="w-full">
          <Button class="w-full text-base h-auto py-6 mt-5">Login</Button>
        </RouterLink>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useAuthStore } from '@/stores/auth'
import Button from '../ui/button/Button.vue'
import { computed } from 'vue'

const props = defineProps({
  gamemode: String,
  desc: String,
  img: String
})

const link = `/src/assets/img/${props.img}.jpg`

const auth = useAuthStore()

const guest_multiplayer = computed(() => {
  return !auth.user && props.gamemode === 'Multiplayer'
})
</script>
