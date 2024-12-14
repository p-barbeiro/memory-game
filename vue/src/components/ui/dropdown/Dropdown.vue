<template>
  <div class="flex items-center text-gray-600 text-sm">
    <div v-if="text" class="text-gray-400 text-nowrap mr-3">{{text}}</div>
    <DropdownInput v-model="selected" :options="props.options" class="w-full" />
  </div>
</template>

<script setup>
import DropdownInput from './DropdownInput.vue'

const selected = defineModel()

const props = defineProps({
  text: {
    type: String,
    default: ''
  },
  options: {
    type: Array,
    required: true
  },
})
</script>
