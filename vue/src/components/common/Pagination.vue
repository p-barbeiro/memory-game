<template>
  <div class="flex items-center text-sm mt-3 md:mt-0 gap-0.5">
    <div class="select-none rounded border border-gray-100 h-10 place-content-center w-full text-center px-2" :class="{ 'text-gray-200 border-gray-100 bg-gray-50 select-none cursor-not-allowed': props.currentPage === 1, 'cursor-pointer text-gray-600 hover:outline-1 hover:outline hover:outline-indigo-100 hover:bg-indigo-50/50': props.currentPage !== 1 }" @click="previousPage">Previous</div>
    <div v-for="page in visiblePages" :key="page" class="cursor-pointer hover:outline-1 hover:outline hover:outline-indigo-100 hover:bg-indigo-50/50 select-none rounded border border-gray-100 h-10 text-gray-600 place-content-center w-full text-center px-3" :class="{ 'bg-indigo-50 font-semibold border-indigo-200': page === props.currentPage }" @click="changePage(page)">{{ page }}</div>
    <div class="select-none rounded border h-10 place-content-center border-gray-100 w-full text-center px-2" :class="{ 'text-gray-200 border-gray-100 bg-gray-50 select-none cursor-not-allowed': props.currentPage === props.lastPage, 'cursor-pointer text-gray-600 hover:outline-1 hover:outline hover:outline-indigo-100 hover:bg-indigo-50/50': props.currentPage !== props.lastPage }" @click="nextPage">Next</div>
  </div>
</template>

<script setup>
import { computed, watch } from 'vue'

const props = defineProps({
  currentPage: Number,
  lastPage: Number,
})

const visiblePages = computed(() => {
  const currentPage = props.currentPage
  const lastPage = props.lastPage
  const pages = []

  if (lastPage <= 5) {
    for (let i = 1; i <= lastPage; i++) {
      pages.push(i)
    }
  } else {
    if (currentPage <= 3) {
      for (let i = 1; i <= 5; i++) {
        pages.push(i)
      }
    } else if (currentPage >= lastPage - 2) {
      for (let i = lastPage - 4; i <= lastPage; i++) {
        pages.push(i)
      }
    } else {
      for (let i = currentPage - 2; i <= currentPage + 2; i++) {
        pages.push(i)
      }
    }
  }
  return pages
})

const emit = defineEmits(['previousPage', 'changePage', 'nextPage'])

const previousPage = () => {
  if (props.currentPage === 1) return
  emit('previousPage')
}

const nextPage = () => {
  if (props.currentPage === props.lastPage) return
  emit('nextPage')
}

const changePage = (page) => {
  emit('changePage', page)
}

watch(() => props.lastPage, (lastPage) => {
  if (props.currentPage > lastPage) {
	emit('changePage', 1)
  }
})

</script>
