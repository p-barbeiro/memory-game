<template>
  <div class="border rounded-xl bg-gradient-to-t from-indigo-800/50 via-indigo-800/20 to-opacity-100">
    <div class="select-none relative group h-52 my-3 place-items-center overflow-clip">
      <img class="object-cover object-center h-full" :src="img" alt="Coins Image" />
    </div>
    <div class="bg-indigo-50 mx-3 p-3 min-h-40 flex flex-col justify-around border rounded-md mb-3">
      <p v-if="!custom" class="font-normal text-xl text-gray-800 text-center h-10">{{ amount }} Brain Coins</p>
      <NumberFieldRoot v-if="custom" id="age" :min="10" :step="10" v-model:modelValue="customTotal" class="mb-2">
        <div class="flex w-full justify-center items-center h-10">
          <NumberFieldDecrement class="p-2 disabled:opacity-20 rounded-l bg-slate-50 h-full w-10 border hover:bg-slate-200">-</NumberFieldDecrement>
          <NumberFieldInput class="text-sm w-full border-y place-items-end pr-1.5 h-full bg-white focus:outline-none" />
          <span class="flex items-center text-sm text-gray-500 text-nowrap pl-1.5 relative bg-white h-full border-y w-full">Brain Coins</span>
          <NumberFieldIncrement class="p-2 disabled:opacity-50 rounded-r bg-slate-50 h-full w-10 border hover:bg-slate-200">+</NumberFieldIncrement>
        </div>
      </NumberFieldRoot>
      <p class="font-semibold text-xl text-gray-800 text-center">{{ total + '€' }}</p>
      <Button class="w-full mt-3" @click="router.push({ name: 'checkout', params: {amount: total} })">Buy</Button>
    </div>
  </div>
</template>

<script setup>
import { NumberFieldDecrement, NumberFieldIncrement, NumberFieldInput, NumberFieldRoot } from 'radix-vue'
import { computed, ref } from 'vue'
import Button from '../ui/button/Button.vue'
import { useRouter } from 'vue-router'

const router = useRouter()

const props = defineProps({
  amount: {
    type: Number,
    required: false
  },
  img: {
    type: String,
    default: '1'
  },
  custom: {
    type: String,
    required: false
  }
})

const customTotal = ref(50)

const total = computed(() => {
  return props.amount ? props.amount / 10 : customTotal.value / 10
})
</script>
