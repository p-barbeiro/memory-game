<template>
	
  <tr class="h-10 border-t border-b border-gray-100">
	<td v-for="column in columns" class="text-center text-sm text-gray-600">{{model[column.key]}}</td>
</tr>
</template>

<script setup>
import IconTimer from '../icons/IconTimer.vue';
import IconBoard from '../icons/IconBoard.vue';

const props = defineProps({
  model: { type: Object, required: true },
  columns: { type: Array, required: true }
})



</script>