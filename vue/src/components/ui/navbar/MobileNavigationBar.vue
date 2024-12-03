<template>
  <ul class="z-40 p-2 rounded-t-none border-r bg-white absolute rounded top-0 left-0 right-0 shadow mt-16 md:mt-16 hidden">
    <MobileMenuItem v-for="menu in menus" :name="menu.name" :link="menu.link" :dropdownList="menu.dropdownList" />

    <!-- Divider -->
    <hr />

    <div v-if="auth.user">
      <!-- User Profile -->
      <MobileMenuProfile />
      <hr />
      <div class="flex items-center">
        <IconProfile />
        <MobileMenuItem name="My Profile" link="home" :bold="false" />
      </div>

      <MobileMenuItem name="Logout" @click="logout" :bold="false" />
    </div>
    <div v-else>
      <MobileMenuItem name="Register" link="home" :bold="false" />
      <MobileMenuItem name="Login" link="login" :bold="false" />
    </div>
  </ul>
</template>

<script setup>
import { globalEvent } from '@/lib/utils'
import IconProfile from '../../icons/IconProfile.vue'
import MobileMenuItem from './MobileMenuItem.vue'
import MobileMenuProfile from './MobileMenuProfile.vue'
import { useAuthStore } from '@/stores/auth'

const auth = useAuthStore()

const props = defineProps({
  menus: { type: Array, required: true }
})

function logout() {
  globalEvent.value.set('logout', true)
}
</script>
