<template>
  <div class="w-full h-[calc(100%-6rem)] md:rounded-xl md:shadow md:bg-white">
    <div class="flex justify-center items-center w-full h-full">
      <div v-if="loading" class="flex justify-center items-center w-full h-full">
        <IconLoading :size="40"></IconLoading>
      </div>
      <div v-else class="flex flex-col flex-grow h-full justify-between overflow-clip">
        <div v-show="finished" class="absolute top-0 left-0 flex justify-center items-center w-full h-full z-10">
          <div class="flex flex-col items-center">
            <h1 class="text-4xl font-bold py-5">Congratulations!</h1>
            <p class="text-lg">
              You have completed the game in <b>{{ turns }}</b> turns!
            </p>
            <p class="text-lg">
              Your time: <b>{{ time }}</b> s
            </p>
            <div class="flex  flex-row gap-5">
              <Button @click="router.back()" class="mt-5">Back</Button>
              <Button @click="router.push({ name: 'home' })" class="mt-5" variant="outline">Game History</Button>
            </div>
          </div>
        </div>
        <GameBar v-show="!finished" :time="time" :pairs="{ totalPairs, pairsFound }" :board="board" :highscore="highscore" />
        <GameChest @click="startTimer" :board="board" :background="background" :cards="cards" @card-click="handleFlip" />
      </div>
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
import { useRouter } from 'vue-router'

const router = useRouter()

const storeBoard = useBoardStore()

const props = defineProps({
  id: {
    type: Number,
    required: true
  }
})

const loading = ref(true)

//timer
const time = ref(0)
const timerStatus = ref(false)
let timer = null

const startTimer = () => {
  if (timer) return // Prevent multiple intervals

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
  return storeBoard.getBoard(props.id)
})

//cards
const cards = ref([])
const images = []
const deck = 'deck2'
const background = `/src/assets/decks/${deck}/empty.png`

const fillDeck = () => {
  for (let i = 1; i <= totalPairs.value; i++) {
    images.push(`/src/assets/decks/${deck}/${i}.svg`)
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
  // cards.value = cards.value.sort(() => Math.random() - 0.5)
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
  finished.value = true
}

const resetFlippedCards = () => {
  flippedCards.value.forEach((card) => {
    card.flipped = false
  })
  flippedCards.value = []
}

//highscore
const auth = useAuthStore()
const storeError = useErrorStore()
const highscore = ref("0")
const fetchHighscore = async () => {
  storeError.resetMessages()
  try {
    axios.defaults.headers.common.Authorization = 'Bearer ' + auth.token
    const url = `users/me/scoreboard?board=${props.id}&filter=time`
    const response = await axios.get(url)
    highscore.value = response.data?.data[0]?.total_time ?? 0
  } catch (e) {
    storeError.setErrorMessages(e.response.data.message, e.response.data.errors, e.response.status, 'Error fetching Boards!')
    return false
  }
}

onMounted(async () => {
  try {
    await storeBoard.fetchBoard(1)
    if (auth.user) {
      await fetchHighscore()
    }
    buildDeck()
    loading.value = false
  } catch (error) {
    console.error('Error during onMounted:', error)
  }
})
</script>
