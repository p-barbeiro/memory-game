<template>
  <li v-if="dropdownList" ref="dropdown" class="hover:text-indigo-900 cursor-pointer h-full flex items-center text-sm text-gray-800 tracking-normal relative mx-2" @click="dropdownHandler($event)">
    <ul class="bg-white shadow rounded py-1 w-32 mt-16 -ml-4 absolute hidden top-0 rounded-t-none">
      <div v-for="item in dropdownList" v-show="item.visible">
        <RouterLink :to="{ name: item.link }">
          <li class="cursor-pointer text-gray-600 text-sm leading-3 tracking-normal py-3 hover:bg-indigo-900 hover:text-white px-3 font-normal h-full w-full">
            {{ item.name }}
          </li>
        </RouterLink>
      </div>
    </ul>
    {{ name }}
    <span class="ml-2">
      <IconDropdown />
    </span>
  </li>
  <RouterLink v-else :to="{ name: link }">
    <li class="hover:text-indigo-900 hover:font-bold cursor-pointer h-full flex items-center text-sm text-gry-800 tracking-normal mx-2">
      {{ name }}
    </li>
  </RouterLink>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue'
import IconDropdown from '@/components/icons/IconDropdown.vue'

const props = defineProps({
  name: { type: String, required: true },
  link: { type: String, required: false },
  dropdownList: { type: Array, required: false }
})

const dropdown = ref(null)

function dropdownHandler(event) {
  let single = event.currentTarget.getElementsByTagName('ul')[0]
  single.classList.toggle('hidden')
}

function handleClickOutside(event) {
  if (dropdown.value && !dropdown.value.contains(event.target)) {
    let single = dropdown.value.getElementsByTagName('ul')[0]
    single.classList.add('hidden')
  }
}

onMounted(() => {
  document.addEventListener('click', handleClickOutside)
})

onBeforeUnmount(() => {
  document.removeEventListener('click', handleClickOutside)
})
</script>
