<template>
  <div class="border rounded-xl border-indigo-200 bg-indigo-50">
    <div class="select-none relative group h-32 my-3 place-items-center overflow-clip">
      <img class="object-cover object-center h-full" :src="img" alt="Coins Image" />
    </div>
    <div class="bg-slate-50 border-indigo-200 mx-3 p-2 min-h-36 flex flex-col justify-around border rounded-lg mb-3 gap-3">
      <p v-if="!custom" class="font-normal text-lg text-indigo-900 text-center items-center flex justify-center">{{ amount }} Brain Coins</p>
      <NumberFieldRoot v-if="custom" id="age" :min="10" :step="10" v-model:modelValue="customTotal">
        <div class="flex w-full justify-center items-center h-10">
          <NumberFieldDecrement class="p-2 disabled:opacity-20 rounded-l bg-slate-50 h-full w-10 border hover:bg-slate-200">-</NumberFieldDecrement>
          <NumberFieldInput class="text-sm w-full border-y place-items-end pr-1.5 h-full bg-white focus:outline-none" />
          <span class="flex items-center text-sm text-gray-500 text-nowrap pl-1.5 relative bg-white h-full border-y w-full">Brain Coins</span>
          <NumberFieldIncrement class="p-2 disabled:opacity-50 rounded-r bg-slate-50 h-full w-10 border hover:bg-slate-200">+</NumberFieldIncrement>
        </div>
      </NumberFieldRoot>
      <p class="font-semibold text-base text-indigo-950 text-center">{{ total + '€' }}</p>
      <Button class="w-full" @click="router.push({ name: 'checkout', params: { amount: total } })">Buy</Button>
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

const customTotal = ref(150)

const total = computed(() => {
  return props.amount ? props.amount / 10 : customTotal.value / 10
})
</script>
