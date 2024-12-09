<script setup>
import Navbar from '@/components/ui/navbar/Navbar.vue'
import { onMounted, provide, useTemplateRef } from 'vue'
import GlobalAlertDialog from './components/common/GlobalAlertDialog.vue'
import Footer from './components/ui/navbar/Footer.vue'
import Toaster from './components/ui/toast/Toaster.vue'
import Debug from './lib/Debug.vue'
import { useAuthStore } from './stores/auth'
import { useBoardStore } from './stores/board'
import GlobalInputDialog from './components/common/GlobalInputDialog.vue'

const storeAuth = useAuthStore()
const storeBoard = useBoardStore()
const storeBoards = useBoardStore()

onMounted( () => {
  storeBoards.fetchBoards()
})

const alertDialog = useTemplateRef('alert-dialog')
provide('alertDialog', alertDialog)

const inputDialog = useTemplateRef('input-dialog')
provide('inputDialog', inputDialog)

const navigation = [
  {
    name: "Play",
    link: 'gamemode'
  },
  {
    name: 'History',
    link: 'history'
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

<template>
  <Debug />
  <Toaster />
  <GlobalAlertDialog ref="alert-dialog"></GlobalAlertDialog>
  <GlobalInputDialog ref="input-dialog"></GlobalInputDialog>

  <div class="md:bg-gray-100">
    <Navbar :menu-items="navigation" />
    <div class="md:container flex justify-center items-center min-h-[calc(100vh-15rem)] md:h-[calc(100vh-13rem)]">
      <router-view />
    </div>
  </div>
  <Footer />
</template>
