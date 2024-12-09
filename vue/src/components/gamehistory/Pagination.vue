<template>
  <nav class="flex justify-center items-center space-x-2" aria-label="Pagination">
    <!-- Previous Button -->
    <button @click="goToPage(currentPage - 1)" :disabled="currentPage === 1" class="px-4 py-2 border rounded-md text-sm font-medium hover:bg-gray-200 disabled:opacity-50 disabled:cursor-not-allowed">Previous</button>

    <!-- Page Numbers -->
    <button v-for="page in visiblePages" :key="page" @click="goToPage(page)" :class="['px-4 py-2 border rounded-md text-sm font-medium', currentPage === page ? 'bg-blue-500 text-white' : 'hover:bg-gray-200']">
      {{ page }}
    </button>

    <!-- Next Button -->
    <button @click="goToPage(currentPage + 1)" :disabled="currentPage === totalPages" class="px-4 py-2 border rounded-md text-sm font-medium hover:bg-gray-200 disabled:opacity-50 disabled:cursor-not-allowed">Next</button>
  </nav>
</template>

<script setup>
import { computed, ref, watch } from 'vue'

const props = defineProps({
  totalItems: {
    type: Number,
    required: true
  },
  itemsPerPage: {
    type: Number,
    default: 10
  },
  currentPageProp: {
    type: Number,
    default: 1
  },
  maxVisiblePages: {
    type: Number,
    default: 5
  }
})

defineEmits(['update:currentPage'])

// Reactive current page
const currentPage = ref(props.currentPageProp)

// Compute total pages
const totalPages = computed(() => Math.ceil(props.totalItems / props.itemsPerPage))

// Compute visible pages
const visiblePages = computed(() => {
  const half = Math.floor(props.maxVisiblePages / 2)
  let start = Math.max(currentPage.value - half, 1)
  let end = start + props.maxVisiblePages - 1

  if (end > totalPages.value) {
    end = totalPages.value
    start = Math.max(end - props.maxVisiblePages + 1, 1)
  }

  return Array.from({ length: end - start + 1 }, (_, i) => start + i)
})

// Update page function
const goToPage = (page) => {
  if (page >= 1 && page <= totalPages.value) {
    currentPage.value = page
    emit('update:currentPage', page)
  }
}

// Watch prop changes
watch(
  () => props.currentPageProp,
  (newVal) => {
    currentPage.value = newVal
  }
)
</script>
