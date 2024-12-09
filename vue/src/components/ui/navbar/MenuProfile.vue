<template>
  <div v-if="isLoading" class="w-full flex items-center justify-end relative">
    <IconLoading />
  </div>
  <div v-else class="w-full flex items-center justify-end relative">
    <div v-if:="auth.user" ref="dropdown" aria-haspopup="true" class="cursor-pointer w-full flex items-center justify-end relative" @click="dropdownHandler($event)">
      <ul class="p-2 w-40 rounded-t-none border-r bg-white absolute rounded z-40 mt-40 hidden">
        <li class="cursor-pointer text-gray-600 text-sm leading-3 tracking-normal py-2 hover:text-indigo-900 hover:font-bold focus:text-indigo-900 focus:outline-none">
          <RouterLink :to="{ name: 'profile' }" class="flex items-center">
            <IconProfile />
            <span class="ml-2"> My Profile </span>
          </RouterLink>
        </li>
        <li class="cursor-pointer text-gray-600 text-sm leading-3 tracking-normal mt-2 py-2 hover:text-indigo-900 hover:font-bold flex items-center focus:text-indigo-900 focus:outline-none" @click="logout">
          <IconLogout />
          <span class="ml-2"> Logout </span>
        </li>
      </ul>
      <img class="rounded-full h-10 w-10 object-cover" :src="auth.userPhotoUrl" alt="avatar" />
      <div class="text-gray-800 text-sm ml-2">
        {{ auth.userName }}
        <div v-if="!auth.isPlayer" class="text-xs font-light text-gray-400">
          {{ auth.userType }}
        </div>
      </div>
    </div>
    <div v-else class="w-full flex items-center justify-end relative gap-x-2">
      <Button @click="router.push({ name: 'register' })" variant="outline">Register</Button>
      <Button @click="router.push({ name: 'login' })"> Login </Button>
    </div>
  </div>
</template>

<script setup>
import IconLogout from '@/components/icons/IconLogout.vue'
import IconProfile from '@/components/icons/IconProfile.vue'
import { isLoading } from '@/lib/utils'
import { useAuthStore } from '@/stores/auth'
import { inject, onBeforeUnmount, onMounted, ref } from 'vue'
import { useRouter } from 'vue-router'
import IconLoading from '../../icons/IconLoading.vue'
import Button from '../button/Button.vue'

const router = useRouter()
const auth = useAuthStore()
const dropdown = ref(null)
const alertDialog = inject('alertDialog')

const logout = () => {
  alertDialog.value.open(logoutConfirmed, 'Logout confirmation?', 'Cancel', `Yes, I want to log out`, `Are you sure you want to log out? You can still access your account later with your credentials.`)
}

const logoutConfirmed = () => {
  auth.logout()
  router.push({ name: 'home' })
}

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
