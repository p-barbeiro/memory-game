<template>
  <div class="md:w-1/3">
    <div class="py-5 px-4 bg-gray-100 border border-indigo-100 shadow rounded-xl text-left h-full flex flex-col justify-between">
      <div :class="{ 'blur-md select-none': !auth.user && !board.guest_enable }">
        <h4 class="text-6xl text-indigo-950 font-bold py-5 text-center">{{ board.columns }} x {{ board.rows }}</h4>
        <div class="text-lg text-center">{{ board.description }}</div>
      </div>
      <div>
        <p v-if="board.price && auth.isPlayer" class="text-base text-indigo-950 flex flex-row items-center mt-4 gap-x-2 justify-center">
          <span class="text-base">Price:</span>
          <span class="text-2xl font-semibold">{{ board.price }}</span>
          <IconCoin class="w-6 h-6" />
        </p>
          <Button @click="newGame(props.board.id)" v-if="(board.guest_enable && auth.user==null) || auth.isPlayer " class="w-full text-base py-6 mt-5">Play</Button>
        <div v-if="!board.guest_enable && !auth.user">
          <div class="text-center"><b>Create</b> an account or <b>login</b> to choose this board.</div>
          <div class="flex flex-row gap-5">
            <Button @click="router.push({ name: 'register' })" class="w-full text-base py-6 mt-5" variant="outline">Register</Button>
            <Button @click="router.push({ name: 'login' })" class="w-full text-base py-6 mt-5">Login</Button>
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
import { useRouter } from 'vue-router'
import { useGameStore } from '@/stores/game'
import { useTransactionStore } from '@/stores/transaction'
import { useToast } from '../ui/toast'

const { toast } = useToast()
const game = useGameStore()
const router = useRouter()
const auth = useAuthStore()
const transaction = useTransactionStore()

const props = defineProps({
  board: {
    type: Object,
    required: true
  }
})

const newGame = async (boardID) => {
  if (auth.user) {
    if(props.board.price) {
      if (auth.user.brain_coins >= props.board.price){
        const responseGame = await game.newGame(boardID)
        transaction.newTransaction({type: 'I',brain_coins: -props.board.price, game_id: responseGame.id,description: `Board ${props.board.columns}x${props.board.rows} unlocked! -${props.board.price} coins`})
      }else{
        toast({
          title: `You don't have enough coins to play with this board.`,
          description: `You can buy more coins in the store.`,
          variant: 'destructive'
        })
      }
    }else{
      console.log(boardID);
      
      game.newGame(boardID)
    }
  } else {
    router.push({ name: 'game' })
  }
}
</script>
