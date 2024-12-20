<template>
  <div v-if="loading" class="place-self-center flex justify-center items-center w-full h-full">
    <IconLoading :size="40"></IconLoading>
  </div>
  <div v-else class="flex flex-col w-full h-full justify-between">
    <div v-if="finished" class="absolute top-0 left-0 flex justify-center items-center w-full h-full z-10">
      <div class="flex flex-col items-center">
        <h1 class="text-4xl font-bold py-5">Congratulations!</h1>
        <p class="text-lg">
          You have completed the game in <b>{{ turns }}</b> turns!
        </p>
        <p class="text-lg">
          Your time: <b>{{ time }}</b> s
        </p>
        <div class="flex flex-row gap-5">
          <Button @click="router.back()" class="mt-5">Back</Button>
          <Button v-if="auth.user" @click="router.push({ name: 'history' })" class="mt-5" variant="outline">Game History</Button>
        </div>
      </div>
    </div>
    <div v-else class="flex flex-col justify-center items-center h-full">
      <GameBar v-show="!finished" :time="time" :pairs="{ totalPairs, pairsFound }" :board="board" :highscore="parseFloat(highscore)" :gameid="props.gameid" />
      <GameChest :board="board" :cards="cards" @card-click="handleFlip" />
    </div>
  </div>
</template>

<script setup>
import IconLoading from '@/components/icons/IconLoading.vue'
import { useAuthStore } from '@/stores/auth'
import { useBoardStore } from '@/stores/board'
import { useErrorStore } from '@/stores/error'
import axios from 'axios'
import { computed, onMounted, ref } from 'vue'
import GameBar from './GameBar.vue'
import GameChest from './GameChest.vue'
import Button from '../ui/button/Button.vue'
import { onBeforeRouteLeave, useRouter } from 'vue-router'
import { useGameStore } from '@/stores/game'

const gameStore = useGameStore()
const router = useRouter()
const auth = useAuthStore()
const storeBoard = useBoardStore()

const game = ref(null)

const props = defineProps({
  gameid: {
    type: Number,
    required: false
  }
})

const loading = ref(true)

//timer
const time = ref(0)
const timerStatus = ref(false)
let timer = null

const startTimer = () => {
  if (timer) return // Prevent multiple intervals

  if (auth.user) gameStore.startGame(props.gameid)

  timerStatus.value = true
  time.value = 0

  timer = setInterval(() => {
    if (timerStatus.value) {
      time.value = parseFloat((time.value + 0.01).toFixed(2)) // Ensure precision
    }
  }, 10)
}

const stopTimer = () => {
  timerStatus.value = false
  clearInterval(timer)
  timer = null // Reset the timer reference
}

//board
const board = computed(() => {
  if (auth.user) return game.value?.board
  return storeBoard.getBoard(1)
})

//cards
const cards = ref([])
const images = []
const deck = 'deck4'

const fillDeck = () => {
  for (let i = 1; i <= totalPairs.value; i++) {
    images.push(`/decks/${deck}/${i}.svg`)
  }
}
const generateCards = () => {
  images.forEach((image, index) => {
    cards.value.push({ id: index + 1, image, flipped: false, matched: false })
    cards.value.push({ id: index + 1, image, flipped: false, matched: false })
  })
}
const buildDeck = () => {
  fillDeck()
  generateCards()
  cards.value = cards.value.sort(() => Math.random() - 0.5)
}

//game
const pairsFound = ref(0)
const flippedCards = ref([])
const turns = ref(0)
const finished = ref(false)
const totalPairs = computed(() => {
  return (board.value.rows * board.value.columns) / 2
})

const handleFlip = (card) => {
  startTimer()
  // If there are less than 2 flipped cards
  if (flippedCards.value.length < 2) {
    card.flipped = true
    flippedCards.value.push(card)

    // If there are 2 flipped cards
    if (flippedCards.value.length === 2) {
      turns.value += 1

      // Check for a match immediately
      setTimeout(() => {
        if (checkMatch()) {
          flippedCards.value = []
        } else {
          setTimeout(() => {
            resetFlippedCards()
          }, 700)
        }
      }, 300)
    }
  }
}

const checkMatch = () => {
 
  const [card1, card2] = flippedCards.value
  if (card1.id === card2.id) {
    card1.matched = true
    card2.matched = true
    pairsFound.value += 1

    if (pairsFound.value === totalPairs.value) {
      gameFinished()
    }

    return true // Cards match
  }
  return false // Cards do not match
  
}

const gameFinished = () => {
  stopTimer()
  if (auth.user) gameStore.updateGame(props.gameid, { total_turns_winner: turns.value, total_time: time.value, status: 'E' })
  finished.value = true
}

const resetFlippedCards = () => {
  flippedCards.value.forEach((card) => {
    card.flipped = false
  })
  flippedCards.value = []
}

//highscore
const storeError = useErrorStore()
const highscore = ref('0')
const fetchHighscore = async () => {
  storeError.resetMessages()
  try {
    axios.defaults.headers.common.Authorization = 'Bearer ' + auth.token
    
    const url = `games?page=1&type=S&status=E&board=${board.value.id}&sort_by=total_time&sort_direction=asc`
    const response = await axios.get(url)
    highscore.value = response.data?.data[0]?.total_time ?? 0
  } catch (e) {
    storeError.setErrorMessages(e.response.data.message, e.response.data.errors, e.response.status, 'Error fetching highscore!')
    return false
  }
}

onMounted(async () => {
  try {
    if (auth.user) {
      game.value = await gameStore.fetchGame(props.gameid)
      await fetchHighscore()
    } else {
      await storeBoard.fetchBoard(1)
    }
    buildDeck()
    loading.value = false
  } catch (error) {
    console.error('Error during onMounted:', error)
  }
})
</script>
