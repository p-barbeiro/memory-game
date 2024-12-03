<template>
  <div class="bg-indigo-950 py-5 relative z-10 h-16">
    <div class="container px-6 mx-auto flex flex-col lg:flex-row items-start lg:items-center justify-between">
      <div class="flex-col flex lg:flex-row items-start lg:items-center">
        <div class="ml-0 lg:ml-0 lg:my-0">
          <h4 class="text-2xl font-bold leading-tight text-white">
            {{ currentPage }}
          </h4>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
const props = defineProps({
  currentPage: String
})
</script>
