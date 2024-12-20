<template>
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
</template>

<script setup>
import { useAuthStore } from '@/stores/auth'
import { inject, ref } from 'vue'
import { onBeforeRouteLeave, useRouter } from 'vue-router'
import Button from '../ui/button/Button.vue'
import GameBoard from './GameBoard.vue'
import GameHighscore from './GameHighscore.vue'
import GamePairs from './GamePairs.vue'
import GameTimer from './GameTimer.vue'
import { useGameStore } from '@/stores/game'

const game = useGameStore()
const auth = useAuthStore()
const props = defineProps({
  time: Number,
  pairs: Object,
  board: Object,
  highscore: Number,
  gameid: Number
})

const router = useRouter()
const alertDialog = inject('alertDialog')

const exitByCancel = ref(false)
const cancel = () => {
  alertDialog.value.open(cancelConfirmed, 'Cancel confirmation?', 'Cancel', `Yes, I want to cancel`, `Are you sure you want to cancel the game?`, exitByCancel.value = false)
}

const cancelConfirmed = () => {
  exitByCancel.value = true
  if(auth.user){
    game.cancelGame(props.gameid)
  }
  router.back()
}

onBeforeRouteLeave((to, from, next) => {
  if (!exitByCancel.value) {
    showConfirmationDialog('Cancel confirmation?', 'Are you sure you want to cancel the game?', 'Cancel', 'Yes, I want to cancel').then((confirmed) => {
      if (confirmed) {
        if(auth.user){
          cancelConfirmed()
        }
        next() // Proceed to the next route
      } else {
        next(false) // Stay on the current route
      }
    })
  } else {
    next() // No confirmation needed, proceed
  }
})

function showConfirmationDialog(title, description, cancelLabel, actionLabel) {
  return new Promise((resolve) => {
    alertDialog.value.open(
      () => resolve(true), // Action callback
      title,
      cancelLabel,
      actionLabel,
      description,
      () => resolve(false) // Cancel callback
    )
  })
}
</script>
