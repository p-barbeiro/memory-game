<template>
  <div class="md:w-1/2">
    <div class="py-5 px-4 md:px-0 md:py-0 bg-gray-100 border border-indigo-100 shadow rounded-xl text-left h-full overflow-hidden flex flex-col justify-between">
      <div class="h-2/3">
        <img :class="{ 'blur-md select-none': multiplayerDisabled }" :src="link" alt="Game Mode" class="w-full h-full object-cover rounded md:rounded-b-none md:rounded-t-xl" />
      </div>
      <div :class="{ 'blur select-none': multiplayerDisabled }" class="md:py-5 md:px-4">
        <div :class="{ 'blur select-none': multiplayerDisabled }" class="text-center pt-5">{{ desc }}</div>
        <p v-if="!multiplayerDisabled && gamemode == 'Multiplayer'" class="text-base text-indigo-950 flex flex-row items-center mt-4 gap-x-2 justify-center">
          <span class="text-base">Required coins:</span>
          <span class="text-2xl font-semibold">5</span>
          <IconCoin class="w-6 h-6" />
        </p>
          <Button v-if="!multiplayerDisabled" @click="handleGamemode" class="w-full text-base h-auto py-3 mt-5">{{ gamemode }}</Button>
      </div>
      <div v-if="multiplayerDisabled" class="text-center"><b>Create</b> an account or <b>login</b> to use multiplayer mode.</div>
      <div v-if="multiplayerDisabled" class="md:pb-5 md:px-4 flex flex-row gap-5">
          <Button @click="router.push({name:'register'})" class="w-full text-base h-auto py-3 mt-5" variant="outline">Register</Button>
          <Button @click="router.push({name:'login'})"class="w-full text-base h-auto py-3 mt-5">Login</Button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { toast } from '@/components/ui/toast';
import { useAuthStore } from '@/stores/auth';
import { computed } from 'vue';
import { useRouter } from 'vue-router';
import IconCoin from '../icons/IconCoin.vue';
import Button from '../ui/button/Button.vue';

const props = defineProps({
  gamemode: String,
  desc: String,
  img: String
})

const link = `/src/assets/img/${props.img}.jpg`
const router = useRouter()
const auth = useAuthStore()

const multiplayerDisabled = computed(() => {
  return !auth.user && props.gamemode === 'Multiplayer'
})

const handleGamemode = () => {
  if (props.gamemode === 'Single-Player') {
    router.push({ name: 'boardSelector' })
  } else {
    handleMultiplayer()
  }
}

const handleMultiplayer = () => {
  console.log('Multiplayer mode selected')
  if (auth.user.brain_coins >= 5) {
    router.push({ name: 'lobby' })
  } else {
    toast({
      title: `You don't have enough coins to play in multiplayer mode.`,
      description: `You can buy more coins in the store.`,
      variant: 'destructive'
    })
  }
}
</script>
