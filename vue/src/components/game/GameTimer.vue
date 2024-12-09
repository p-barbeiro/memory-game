<template>
  <div class="flex flex-row p-3 gap-3 items-center lg:h-full w-full border-b lg:border-none lg:w-1/4 justify-end">
    <IconTimer />
    <div class="w-1/2">
      {{ time.toFixed(2) }} s
    </div>
  </div>
</template>

<script setup>
import IconTimer from '@/components/icons/IconTimer.vue'

const props = defineProps({
  time: Number
})
</script>
