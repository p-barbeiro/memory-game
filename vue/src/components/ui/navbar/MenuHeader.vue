<template>
  <div class="bg-indigo-950 py-2 z-10 h-16 place-content-center">
    <h4 class="container text-lg md:text-xl font-bold text-white truncate">
      {{ currentPage }}
    </h4>
  </div>
</template>

<script setup>
const props = defineProps({
  currentPage: String
})
</script>
