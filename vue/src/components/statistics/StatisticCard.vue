<template>
  <div class="flex justify-center flex-col items-center aspect-square p-2 bg-white shadow rounded-2xl">
    <h2 class="lg:text-5xl md:text-4xl text-2xl font-extrabold leading-10 text-center text-gray-800">{{ data ?? ''}}</h2>
    <p class="mt-4 text-sm md:text-base lg:text-lg leading-none text-center text-gray-600">{{ desc ?? '' }}</p>
  </div>
</template>

<script setup>
const props = defineProps({
  desc: String,
  data: Number
})
</script>
