<template>
  <div class="md:w-1/3 h-full">
    <div class="py-5 px-4 bg-gray-100 border border-indigo-100 shadow rounded-xl text-left h-full flex flex-col justify-between">
      <div :class="{ 'blur-md select-none': !auth.user && !board.guest_enable }">
        <h4 class="text-6xl text-indigo-950 font-bold py-5 text-center">{{ board.columns }} x {{ board.rows }}</h4>
        <div class="text-lg text-center">{{ board.description }}</div>
      </div>
      <div>
        <p v-if="board.price && auth.user" class="text-base text-indigo-950 flex flex-row items-center mt-4 gap-x-2 justify-center">
          <span class="text-base">Price:</span>
          <span class="text-2xl font-semibold">{{ board.price }}</span>
          <IconCoin class="w-6 h-6" />
        </p>
        <RouterLink :to="{ name: 'game', params: { id: board.id } }">
          <Button v-if="board.guest_enable || auth.user" class="w-full text-base py-6 mt-5">Play</Button>
        </RouterLink>
        <div v-if="!board.guest_enable && !auth.user">
          <div class="text-center"><b>Create</b> an account or <b>login</b> to choose this board.</div>
          <div class="flex flex-row gap-5">
            <RouterLink :to="{ name: 'register' }" class="w-full">
              <Button class="w-full text-base py-6 mt-5" variant="outline">Register</Button>
            </RouterLink>
            <RouterLink :to="{ name: 'login' }" class="w-full">
              <Button class="w-full text-base py-6 mt-5">Login</Button>
            </RouterLink>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import IconCoin from '@/components/icons/IconCoin.vue'
import { useAuthStore } from '@/stores/auth'
import Button from '../ui/button/Button.vue'

const auth = useAuthStore()
const props = defineProps({
  board: {
    type: Object,
    required: true
  }
})
</script>
