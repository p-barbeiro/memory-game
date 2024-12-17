<template>
  <div class="grid gap-1 mt-5 content-center w-full md:w-auto" :style="style">
    <div v-for="(card, index) in cards" :key="index" class="md:w-40 relative aspect-square cursor-pointer select-none" @click="handleCardClick(card)">
      <!-- Card front -->
      <div class="absolute bg-gray-50 w-full h-full rounded flex justify-center items-center shadow transition-transform duration-500 transform preserve-3d" :class="{ 'flip-up': card.flipped || card.matched, 'flip-down': !card.flipped && !card.matched, hidden: card.matched }">
        <img v-if="card.flipped || card.matched" :src="card.image" alt="Card" class="w-full h-full object-cover rounded" />
      </div>

      <!-- Card back -->
      <div class="border border-indigo-100 absolute w-full h-full rounded flex justify-center items-center shadow transition-transform duration-500 transform preserve-3d backface-hidden" :class="{ 'flip-up': card.flipped || card.matched, 'flip-down': !card.flipped && !card.matched }">
        <div class="bg-indigo-50 w-full h-full flex justify-center items-center">
          <img :src="MemoryGameLogo" alt="Memory Game Logo" class="w-16 h-16 opacity-10" />
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import MemoryGameLogo from '@/assets/img/logo.svg'
import { computed, onMounted, ref } from 'vue'

const props = defineProps({
  cards: {
    type: Array,
    required: true
  },
  board: {
    type: Object,
    required: true
  }
})

const emit = defineEmits(['card-click'])
// Prevent clicking on flipped or matched cards
const handleCardClick = (card) => {
  if (card.flipped || card.matched) {
    return
  }
  emit('card-click', card)
}

const style = computed(() => {
  return { gridTemplateColumns: `repeat(${props.board?.columns}, 1fr)` }
})
</script>

<style scoped>
/* Tailwind classes for flipping */
.preserve-3d {
  transform-style: preserve-3d;
}

.backface-hidden {
  backface-visibility: hidden;
}

.flip-up {
  transform: rotateY(180deg);
  transition: transform 0.2s ease-in-out; /* Smooth flip-up animation */
}

.flip-down {
  transform: rotateY(0deg);
  transition: transform 0s; /* Instant flip-down */
}
</style>
