<template>
  <!-- Auth User  -->
  <div v-if="auth.user">
    <!-- User Profile -->
    <li class="mx-2 cursor-pointer text-gray-600 text-sm leading-3 tracking-normal py-2 hover:text-blue-900 flex items-center focus:text-blue-900 focus:outline-none">
      <div class="flex flex-row items-center w-full h-full justify-between">
        <div class="flex flex-row items-center">
          <div class="w-16 cursor-pointer flex text-sm border-2 border-transparent rounded focus:outline-none focus:border-white transition duration-150 ease-in-out">
            <img class="rounded h-16 w-16 object-cover border" :src="auth.userPhotoUrl" alt="logo" />
          </div>
          <p class="leading-6 text-base ml-2">{{ auth.userName }}</p>
        </div>

        <div v-if="auth.isPlayer" class="flex flex-row gap-x-2 items-center border w-16 h-16 rounded-md p-2">
          <IconCoin />
          <p class="font-bold">{{ auth.userBrainCoins }}</p>
        </div>
      </div>
    </li>

    <!-- Spacer -->
    <hr />

    <!-- My Profile -->
    <div class="flex items-center">
      <IconProfile />
      <MobileMenuItem name="My Profile" link="profile" :bold="false" />
    </div>

    <div class="flex items-center">
      <IconLogout />
      <MobileMenuItem name="Logout" @click="logout" :bold="false" />
    </div>
  </div>

  <!-- Guest User -->
  <div v-else>
    <MobileMenuItem name="Register" link="register" :bold="false" />
    <MobileMenuItem name="Login" link="login" :bold="false" />
  </div>
</template>

<script setup>
import IconCoin from '@/components/icons/IconCoin.vue'
import IconProfile from '@/components/icons/IconProfile.vue'
import { useAuthStore } from '@/stores/auth'
import { inject } from 'vue'
import IconLogout from '../../icons/IconLogout.vue'
import MobileMenuItem from './MobileMenuItem.vue'
const auth = useAuthStore()

const alertDialog = inject('alertDialog')

const logout = () => {
  alertDialog.value.open(logoutConfirmed, 'Logout confirmation?', 'Cancel', `Yes, I want to log out`, `Are you sure you want to log out? You can still access your account later with your credentials.`)
}

const logoutConfirmed = () => {
  auth.logout()
}
</script>
