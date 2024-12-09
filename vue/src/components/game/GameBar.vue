<template>
  <div class="p-3 pb-0">
    <div class="flex flex-col border lg:flex-row lg:gap-3 bg-gray-100 lg:border rounded w-full items-center justify-between">
      <div class="flex flex-col lg:flex-row lg:gap-3 bg-gray-100 w-full lg:h-16">
        <GameTimer :time="time" />
        <!-- Divider -->
        <div class="border-l w-1 h-full"></div>
        <!-- Current highscore -->
        <GamePairs :total="pairs.totalPairs" :found="pairs.pairsFound" />
        <!-- Divider -->
        <div class="border-l w-1 h-full"></div>
        <!-- Current highscore -->
        <GameHighscore v-if="auth.user" :value="highscore" />
        <!-- Divider -->
        <div v-if="auth.user" class="border-l w-1 h-full"></div>
        <!-- Board highscore -->
        <GameBoard :board="board" />
        <div class="border-l w-1 h-full"></div>
      </div>
      <!-- Timer -->
      <div class="p-3">
        <Button @click="cancel"> Cancel </Button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { inject } from 'vue'
import { useRouter } from 'vue-router'
import Button from '../ui/button/Button.vue'
import GameBoard from './GameBoard.vue'
import GameHighscore from './GameHighscore.vue'
import GamePairs from './GamePairs.vue'
import GameTimer from './GameTimer.vue'
import { useAuthStore } from '@/stores/auth'

const auth = useAuthStore()
const props = defineProps({
  time: Number,
  pairs: Object,
  board: Object,
  highscore: String
})

const router = useRouter()
const alertDialog = inject('alertDialog')

const cancel = () => {
  alertDialog.value.open(cancelConfirmed, 'Cancel confirmation?', 'Cancel', `Yes, I want to cancel`, `Are you sure you want to cancel the game?`)
}

const cancelConfirmed = () => {
  console.log('Game cancelled')
  router.back()
}
</script>
