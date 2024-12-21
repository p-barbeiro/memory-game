<template>
  <div class="border rounded-xl border-indigo-200 bg-indigo-50">
    <div class="select-none relative group h-32 my-3 place-items-center overflow-clip">
      <Carousel v-bind="config">
        <Slide v-for="img in 10" :key="img">
          <div class="carousel__item">
            <img :src="props.img + img + '.svg'" alt="Card" class="h-20" />
          </div>
        </Slide>

        <template #addons>
          <Navigation />
        </template>
      </Carousel>
    </div>
    <div class="bg-slate-50 border-indigo-200 mx-3 p-2 min-h-20 flex flex-col justify-around border rounded-lg mb-3 gap-3">
      <p class="font-normal text-lg text-indigo-900 text-center items-center flex justify-center">{{ item }}</p>
      <p class="font-semibold text-base text-indigo-950 text-center">
        {{ isDeckOwned || props.default? 'Owned' : price + ' Brain Coins' }}
      </p>
      <Button v-if="!isDeckOwned && !props.default" class="w-full" @click="makeTransaction">Buy</Button>
      <Badge v-if="isActiveDeck" class="w-full hover:cursor-pointer" variant="indigo-outline"> Current Deck </Badge>
      <Badge v-if="(isDeckOwned && !isActiveDeck) || (props.default && !isActiveDeck)" class="w-full hover:cursor-pointer" variant="indigo" @click="makeDeckActive"> Set Active </Badge>
    </div>
  </div>
</template>

<script setup>
import { computed, ref } from 'vue'
import { useRouter } from 'vue-router'
import { Carousel, Navigation, Slide } from 'vue3-carousel'
import 'vue3-carousel/dist/carousel.css'
import Button from '../ui/button/Button.vue'
import { useAuthStore } from '@/stores/auth'
import { useTransactionStore } from '@/stores/transaction'
import Badge from '../ui/badge/Badge.vue'
import { useToast } from '../ui/toast'

const config = {
  itemsToShow: 3.95,
  wrapAround: true,
  transition: 500
}

const router = useRouter()
const storeAuth = useAuthStore()
const storeTransaction = useTransactionStore()
const {toast} = useToast()

const props = defineProps({
  price: {
    type: Number,
    required: false
  },
  img: {
    type: String,
    default: '1'
  },
  item: {
    type: String,
    default: 'Animals'
  },
  default: {
    type: Boolean,
    default: false
  }
})

// Provide default values if `custom` is empty
const activeDeck = computed(() => storeAuth.custom?.active_deck ?? 'Animals')
const decksOwned = computed(() => storeAuth.custom?.decks ?? [])

// Helpers for button states
const isDeckOwned = computed(() => decksOwned.value.includes(props.item))
const isActiveDeck = computed(() => activeDeck.value === props.item)

// Methods
const makeTransaction = async () => {
  // Perform transaction logic
  if (storeAuth.user.brain_coins < props.price) {
    toast({
      title: `You don't have enough coins to buy with this deck.`,
      description: `You can buy more coins in the store.`,
      variant: 'destructive'
    })
    return
  }

  await storeTransaction.createTransaction({
    type: 'I',
    brain_coins: -props.price,
    description: `Bought '${props.item}'' deck`
  })

  const updatedDecks = storeAuth.custom?.decks ? [...storeAuth.custom.decks, props.item] : [props.item]
  await storeAuth.updateUserItems({
    custom: {
      active_deck: props.item,
      decks: updatedDecks
    }
  })
}

const makeDeckActive = async () => {
  // Update the active deck
  await storeAuth.updateUserItems({
    custom: {
      ...storeAuth.custom,
      active_deck: props.item
    }
  })
}
</script>
