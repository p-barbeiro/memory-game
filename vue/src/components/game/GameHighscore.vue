<template>
	<div class="flex flex-row p-3 gap-3 items-center lg:h-full w-full border-b lg:border-none lg:w-1/4 justify-center">
	            <IconHighscore />
	            Highscore : {{props.value}} s
	          </div>
</template>

<script setup>
import IconHighscore from '@/components/icons/IconHighscore.vue';

const props = defineProps({
  value: Number
})
</script>