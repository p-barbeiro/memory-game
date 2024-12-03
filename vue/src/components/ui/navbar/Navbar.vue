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
          <button @click="toaster">Toaster</button>
        </div>

        <!-- Right side -->
        <div class="h-full xl:flex hidden items-center justify-end">
          <div class="h-full flex items-center">
            <!-- Divider -->
            <div class="w-32 pr-16 h-full flex items-center justify-end border-r"></div>

            <!-- Profile -->
            <div class="w-full h-full flex">
              <MenuProfile />
            </div>
          </div>
        </div>

        <!-- Mobile Navigation -->
        <div class="visible xl:hidden flex items-center">
          <MobileNavigationBar :menus="menuItems" />
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
import IconHambuger from '../../icons/IconHambuger.vue'
import MenuHeader from './MenuHeader.vue'
import MenuProfile from './MenuProfile.vue'

import { computed } from 'vue'
import { useRouter } from 'vue-router'
import MobileNavigationBar from './MobileNavigationBar.vue'
import NavigationBar from './NavigationBar.vue'

const route = useRouter()
const currentPage = computed(() => route.currentRoute.value.meta.title)
const menuItems = [
  {
    name: 'Home',
    link: 'home'
  },
  {
    name: 'Products',
    link: 'login'
  },
  {
    name: 'Scoreboards',
    dropdownList: [
      {
        name: 'Cenas1',
        link: 'home'
      },
      {
        name: 'Cenas2',
        link: 'home'
      }
    ]
  },
  {
    name: 'Testers',
    dropdownList: [
      {
        name: 'Laravel',
        link: 'laravelTester'
      },
      {
        name: 'WebSockets',
        link: 'webSocketTester'
      }
    ]
  }
]


</script>
