<template>

  <div v-if="isLoading" class="w-full flex items-center justify-end relative">
    <IconLoading />
  </div>
  <div v-else class="w-full flex items-center justify-end relative">
    <div v-if:="auth.user" ref="dropdown" aria-haspopup="true" class="cursor-pointer w-full flex items-center justify-end relative" @click="dropdownHandler($event)">
      <ul class="p-2 w-40 rounded-t-none border-r bg-white absolute rounded z-40 mt-40 hidden">
        <li class="cursor-pointer text-gray-600 text-sm leading-3 tracking-normal py-2 hover:text-indigo-900 hover:font-bold focus:text-indigo-900 focus:outline-none">
          <div class="flex items-center">
            <IconProfile />
            <span class="ml-2"> My Profile </span>
          </div>
        </li>
        <li class="cursor-pointer text-gray-600 text-sm leading-3 tracking-normal mt-2 py-2 hover:text-indigo-900 hover:font-bold flex items-center focus:text-indigo-900 focus:outline-none" @click="logout">
          <IconSettings />
          <span class="ml-2"> Logout </span>
        </li>
      </ul>
      <img class="rounded-full h-10 w-10 object-cover" :src="auth.userPhotoUrl" alt="avatar" />
      <p class="text-gray-800 text-sm ml-2">{{ auth.userName }}</p>
    </div>
    <div v-else class="w-full flex items-center justify-end relative gap-x-2">
      <Button variant="outline">Register</Button>
      <RouterLink :to="{ name: 'login' }">
        <Button> Login </Button>
      </RouterLink>
    </div>
  </div>
</template>

<script setup>
import IconProfile from '@/components/icons/IconProfile.vue'
import IconSettings from '@/components/icons/IconSettings.vue'
import { useAuthStore } from '@/stores/auth'
import { onBeforeUnmount, onMounted, ref } from 'vue'
import Button from '../button/Button.vue'
import IconLoading from '../../icons/IconLoading.vue'
import { isLoading } from '@/lib/utils'

import { globalEvent } from '@/lib/utils'
function logout() {
  globalEvent.value.set('logout', true)
}

const auth = useAuthStore()

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
