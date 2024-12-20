<template>
  <div v-if="loading" class="flex justify-center items-center w-full h-full place-self-center">
    <IconLoading :size="40"></IconLoading>
  </div>
  <div v-else class="flex flex-col justify-between w-full h-full" :class="{ 'border-[3px] border-indigo-800/50 rounded-xl shadow ': currentUserTurn }">
    <div v-if="gameEnded" class="top-0 left-0 z-10 flex justify-center items-center w-full h-full">
      <!-- player 1 win -->
      <div class="flex flex-col items-center gap-3">
        <h1 class="py-5 font-bold text-4xl text-center">{{ statusGameMessage }}</h1>
        <p class="text-center text-lg">{{ statusDescription }}</p>
        <p class="text-center text-lg">
          You have found <b>{{ winnerPairs }}</b> pairs!
        </p>
        <div class="flex flex-row gap-5">
          <Button @click="close" class="mt-5">Close</Button>
        </div>
      </div>
    </div>
    <div v-else class="flex flex-col justify-center items-center p-1 md:p-2 h-full">
      <div v-show="!quitIsOpen" class="flex flex-row justify-end opacity-20 hover:opacity-100 pb-5 w-full cursor-pointer">
        <X @click="quitGame" />
      </div>
      <Card class="flex md:flex-row flex-col justify-between items-center gap-3 bg-slate-50 p-1 rounded-b-none w-full md:h-16" :class="{ 'blur-xl': quitIsOpen }">
        <div class="flex flex-row justify-start items-center gap-2 p-2 border-b md:border-none w-full h-full" :class="{ 'bg-indigo-100 rounded-md': props.game.currentPlayer == 1 }">
          <img :src="auth.getPhotoURL(game.player1.photoFileName)" alt="Brain" class="ml-1 rounded-full w-12 h-12" />
          <div class="flex flex-col">
            <span class="font-semibold text-lg">{{ game.player1.nickname }}</span>
            <span class="text-gray-500 text-sm">Player1</span>
          </div>
          <div class="flex flex-row items-end gap-2 ml-auto">
            <span class="font-semibold text-lg">{{ game.player1_pairs / 2 }}</span>
            <IconPairs />
          </div>
        </div>

        <div class="flex flex-row justify-center items-center gap-3 px-3 w-full h-full italic">vs.</div>

        <div class="flex flex-row justify-end items-center gap-2 p-2 border-t md:border-none w-full h-full" :class="{ 'bg-indigo-100 rounded-md': props.game.currentPlayer == 2 }">
          <div class="flex flex-row items-end gap-2 mr-auto">
            <IconPairs />
            <span class="font-semibold text-lg">{{ game.player2_pairs / 2 }}</span>
          </div>
          <div class="flex flex-col">
            <span class="font-semibold text-lg">{{ game.player2.nickname }}</span>
            <span class="text-end text-gray-500 text-sm">Player2</span>
          </div>
          <img :src="auth.getPhotoURL(game.player2.photoFileName)" alt="Brain" class="mr-1 rounded-full w-12 h-12" />
        </div>
      </Card>
      <Card class="flex md:flex-row flex-col justify-between items-center bg-slate-50 p-2 border-t-0 rounded-t-none w-full md:h-10" :class="{ 'blur-xl': quitIsOpen }">
        <div class="flex justify-center items-center border-collapse px-2 h-full">
          Game:
          <span class="pl-1">
            <b>{{ game.id }}</b>
          </span>
        </div>
        <div class="flex justify-center items-center px-2 h-full">
          Total Pairs:
          <span class="pl-1">
            <b>{{ game.pairsFound / 2 }}/{{ `${(game.board.columns * game.board.rows) / 2}` }}</b>
          </span>
        </div>
        <div class="flex justify-center items-center px-2 h-full">
          Board:
          <span class="pl-1">
            <b>{{ `${game.board.columns} x ${game.board.rows}` }}</b>
          </span>
        </div>
      </Card>
      <div v-show="currentUserTurn && !quitIsOpen" class="mt-5"><span class="ml-5 font-semibold text-indigo-950 text-lg animate-[pulse_1s_ease-in-out_infinite]">It's your turn</span></div>

      <GameChest :class="{ 'blur-sm select-none': !currentUserTurn, 'blur-xl': quitIsOpen }" :board="board" :cards="cards" @card-click="handleFlip" />

      <div v-show="!currentUserTurn && !quitIsOpen" class="absolute flex flex-col justify-center items-center gap-2 mt-2">
        <div class="flex flex-row justify-center items-center gap-2 mt-32 md:mt-0">
          <IconLoading :size="10"></IconLoading>
          <span class="ml-5 font-semibold text-lg">It's your opponent's turn</span>
        </div>
      </div>

      <div v-show="quitIsOpen" class="absolute flex flex-col justify-center items-center gap-2 mt-2">
        <div class="flex flex-col justify-center items-center gap-2 border p-5 bg-slate-50 rounded-lg">
          <span class="text-base text-center">Are you sure you want to quit? <br><b>There is no turning back!</b></span>
          <div class="flex flex-row gap-3">
            <Button @click="quitIsOpen = false" variant="outline">Cancel</Button>
            <Button @click="storeMultiplayer.quit(props.game)">Yes, I want to quit</Button>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- <Code :var="game"></Code> -->
</template>

<script setup>
import IconLoading from '@/components/icons/IconLoading.vue'
import { Card } from '@/components/ui/card'
import { useAuthStore } from '@/stores/auth'
import { useMultiplayerStore } from '@/stores/multiplayer'
import { X } from 'lucide-vue-next'
import { computed, onMounted, ref } from 'vue'
import GameChest from '../game/GameChest.vue'
import IconPairs from '../icons/IconPairs.vue'
import Button from '../ui/button/Button.vue'
import { useToast } from '../ui/toast'

const quitIsOpen = ref(false)
const { toast } = useToast()
const auth = useAuthStore()
const storeMultiplayer = useMultiplayerStore()

const quitGame = () => {
  quitIsOpen.value = true
}

const props = defineProps({
  game: {
    type: Object,
    required: true
  }
})

const loading = ref(true)

//board
const board = computed(() => {
  return props.game.board
})

//fill cards with images
const cards = computed(() => {
  return props.game.cards.map((card) => {
    return {
      ...card,
      image: `/decks/deck4/${card.id}.svg`
    }
  })
})

//game
const flippedCards = ref([])
const handleFlip = (card) => {
  if (currentUserTurn.value) {
    // If there are less than 2 flipped cards
    if (flippedCards.value.length < 2) {
      card.flipped = true
      storeMultiplayer.updateCard(props.game, card)
      flippedCards.value.push(card)

      // If there are 2 flipped cards
      if (flippedCards.value.length === 2) {
        setTimeout(() => {
          if (checkMatch()) {
            for (let i = 0; i < flippedCards.value.length; i++) {
              storeMultiplayer.updateCard(props.game, flippedCards.value[i])
            }
            flippedCards.value = []
          } else {
            setTimeout(() => {
              resetFlippedCards()
            }, 700)
          }
        }, 300)
      }
    }
  } else {
    toast({
      title: `It's not your turn.`,
      variant: 'destructive'
    })
  }
}

const checkMatch = () => {
  const [card1, card2] = flippedCards.value
  if (card1.id === card2.id) {
    card1.matched = true
    card2.matched = true
    return true // Cards match
  }
  return false // Cards do not match
}

const resetFlippedCards = () => {
  flippedCards.value.forEach((card) => {
    card.flipped = false
    storeMultiplayer.updateCard(props.game, card)
  })
  flippedCards.value = []
  storeMultiplayer.switchPlayer(props.game)
}

const gameEnded = computed(() => {
  return props.game.gameStatus > 0
})

const currentUserTurn = computed(() => {
  if (gameEnded.value) {
    return false
  }
  return props.game.currentPlayer === storeMultiplayer.playerNumberOfCurrentUser(props.game)
})

const statusDescription = computed(() => {
  switch (props.game.gameStatus) {
    case 1:
    case 2:
      return storeMultiplayer.playerNumberOfCurrentUser(props.game) == props.game.gameStatus ? 'Congratulations, you’re the ultimate memory master! 🏆 Well played!' : `Well played! Don't worry, defeat is just a stepping stone to victory. Better luck next time!`
    case 3:
      return 'It’s a draw! Great minds think alike — well played by both!'
    default:
      return 'Game in progress'
  }
})

const statusGameMessage = computed(() => {
  switch (props.game.gameStatus) {
    case 1:
    case 2:
      return storeMultiplayer.playerNumberOfCurrentUser(props.game) == props.game.gameStatus ? 'Victory 🎉🥇💪' : 'You Lost 🤕🥈👎'
    case 3:
      return "It's a draw 🤝🤷‍♂️"
    default:
      return 'Not started!'
  }
})

const winnerPairs = computed(() => {
  return storeMultiplayer.playerNumberOfCurrentUser(props.game) == 1 ? props.game.player1_pairs / 2 : props.game.player2_pairs / 2
})

const close = () => {
  storeMultiplayer.close(props.game)
}

onMounted(async () => {
  try {
    loading.value = false
  } catch (error) {
    console.error('Error during onMounted:', error)
  }
})
</script>
