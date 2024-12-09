<template>
  <li v-if="dropdownList" class="z-30 xl:hidden flex-col cursor-pointer text-gray-600 text-base leading-3 tracking-normal py-3 hover:text-blue-900 focus:text-blue-900 focus:outline-none flex justify-center" @click="dropdownHandler($event)">
    <div class="flex items-center">
      <span class="leading-6 mx-2 font-bold"> {{ name }} </span>
      <IconDropdown id="dropdown" class="-rotate-90"/>
    </div>
    <ul class="ml-2 mt-3 hidden">
      <RouterLink v-for="item in dropdownList" :to="{ name: item.link }" @click.native="emitCloseMenu">
        <li class="cursor-pointer text-gray-600 text-sm leading-3 tracking-normal py-3 hover:bg-blue-900 hover:text-white px-3 font-normal">
          {{ item.name }}
        </li>
      </RouterLink>
    </ul>
  </li>
  <RouterLink v-else :to="{ name: link }" class="flex xl:hidden cursor-pointer text-gray-600 text-base leading-3 tracking-normal py-3 hover:text-blue-900 focus:text-blue-900 focus:outline-none" @click.native="emitCloseMenu">
    <li>
      <span class="leading-6 ml-2" :class="{ 'font-bold': bold }"> {{ name }} </span>
    </li>
  </RouterLink>
</template>

<script setup>
import IconDropdown from '@/components/icons/IconDropdown.vue'
import { globalEvent } from '@/lib/utils'
const props = defineProps({
  name: { type: String, required: true },
  link: { type: String, required: false, default: 'home' },
  dropdownList: { type: Array, required: false },
  bold: { type: Boolean, default: true }
})

function dropdownHandler(event) {
  let single = event.currentTarget.getElementsByTagName('ul')[0]
  single.classList.toggle('hidden')
  //select the dropdown icon
  let icon = event.currentTarget.getElementsByTagName('svg')[0]
  icon.classList.toggle('-rotate-90')
}

function emitCloseMenu() {
  globalEvent.value.set('closeMenu', true)
}
</script>
