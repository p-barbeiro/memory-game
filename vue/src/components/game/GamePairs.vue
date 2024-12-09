<template>
  <div class="flex flex-row p-3 gap-3 items-center lg:h-full w-full border-b lg:border-none lg:w-1/4 justify-center">
    <IconPairs />
    Pairs found: {{ props.found }}/{{ props.total }}
  </div>
</template>

<script setup>
import IconPairs from '@/components/icons/IconPairs.vue'

const props = defineProps({
  total: Number,
  found: Number
})
</script>
