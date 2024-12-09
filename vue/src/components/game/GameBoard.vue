<template>
	<div class="flex flex-row p-3 gap-3 items-center lg:h-full w-full border-b lg:border-none lg:w-1/4 justify-center">
	            <IconBoard />
	            Board : {{props.board.columns}}x{{props.board.rows}}
	          </div>
</template>

<script setup>
import IconBoard from '@/components/icons/IconBoard.vue';

const props = defineProps({
  board: Object
})



</script>