<template>
  <div>
    <!-- Navbar -->
    <nav class="w-full mx-auto bg-white shadow relative z-20">
      <div class="justify-between container px-6 h-16 flex items-center lg:items-stretch mx-auto">
        <!-- Left side -->
        <div class="flex items-center">
          <!-- Logo-->
          <RouterLink :to="{ name: 'home' }">
            <div class="mr-10 flex items-center">
              <img class="h-10 w-10 object-cover" :src="MemoryGameLogo" alt="logo" />
              <h3 class="text-base text-gray-800 font-bold tracking-normal leading-tight ml-3 lg:block">Memory Game</h3>
            </div>
          </RouterLink>

          <!-- Navigation Links -->
          <NavigationBar :menus="menuItems" />
        </div>

        <!-- Right side -->
        <div class="h-full xl:flex hidden items-center justify-end">
          <div class="h-full flex items-center">
            <div v-if="auth.isPlayer" class="flex flex-row gap-x-2 items-center">
              <IconCoin />
              <div class="font-bold">
                {{ auth.userBrainCoins }}
              </div>
            </div>
            <!-- Divider -->
            <div class="border-r h-full mx-3"></div>
            <!-- Profile -->
            <div class="w-full h-full flex">
              <MenuProfile />
            </div>
          </div>
        </div>

        <!-- Mobile Navigation -->
        <div class="visible xl:hidden flex items-center">
          <ul class="z-20 p-2 rounded-t-none border-r bg-white absolute rounded top-0 left-0 right-0 shadow mt-16 md:mt-16 hidden">
            <MobileNavigationBar :menus="menuItems" />
            <hr />
            <MobileMenuProfile />
          </ul>

          <IconHambuger />
        </div>
      </div>
    </nav>

    <!-- Page Header -->
    <MenuHeader :currentPage="currentPage" />
  </div>
</template>

<script setup>
import MemoryGameLogo from '@/assets/img/logo.svg'
import { useAuthStore } from '@/stores/auth'
import { computed } from 'vue'
import { useRouter } from 'vue-router'
import IconCoin from '../../icons/IconCoin.vue'
import IconHambuger from '../../icons/IconHambuger.vue'
import MenuHeader from './MenuHeader.vue'
import MenuProfile from './MenuProfile.vue'
import MobileMenuProfile from './MobileMenuProfile.vue'
import MobileNavigationBar from './MobileNavigationBar.vue'
import NavigationBar from './NavigationBar.vue'

const route = useRouter()
const auth = useAuthStore()

const currentPage = computed(() => route.currentRoute.value.meta.title)
const props = defineProps({
  menuItems: { type: Array, required: true }
})
</script>
