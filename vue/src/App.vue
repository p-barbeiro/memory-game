<script setup>
import Navbar from '@/components/ui/navbar/Navbar.vue'
import { computed, onMounted, provide, ref, useTemplateRef, inject } from 'vue'
import GlobalAlertDialog from './components/common/GlobalAlertDialog.vue'
import GlobalInputDialog from './components/common/GlobalInputDialog.vue'
import Footer from './components/ui/navbar/Footer.vue'
import Toaster from './components/ui/toast/Toaster.vue'
import Debug from './lib/Debug.vue'
import { useAuthStore } from './stores/auth'
import { useBoardStore } from './stores/board'
import { useChatStore } from '@/stores/chat'

const socket = inject('socket')
const auth = useAuthStore()
const storeBoard = useBoardStore()
const storeBoards = useBoardStore()
const storeChat = useChatStore()

onMounted(() => {
  storeBoards.fetchBoards()
})

const alertDialog = useTemplateRef('alert-dialog')
provide('alertDialog', alertDialog)

const inputDialog = useTemplateRef('input-dialog')
provide('inputDialog', inputDialog)

let userDestination = null
socket.on('privateMessage', (messageObj) => {
  userDestination = messageObj.user
  inputDialog.value.open(handleMessageFromInputDialog, 'Message from ' + messageObj.user.name, `This is a private message sent by ${messageObj?.user?.name}!`, 'Reply Message', '', 'Close', 'Reply', messageObj.message)
})

const handleMessageFromInputDialog = (message) => {
  storeChat.sendPrivateMessageToUser(userDestination, message)
}

const toPlayer = computed(() => auth.isPlayer)
const toAdmin = computed(() => auth.isAdmin)
const toGuest = computed(() => !auth.user)
const toGuestPlayer = computed(() => toPlayer.value || toGuest.value)


const navigation = ref([
  {
    name: 'Users',
    link: 'allUsers',
    visible: toAdmin
  },
  {
    name: 'Transactions',
    link: 'allTransactions',
    visible: toAdmin
  },
  {
    name: 'Boards',
    link: 'allBoards',
    visible: toAdmin
  },
  {
    name: 'Games',
    link: 'allGames',
    visible: toAdmin
  },
  {
    name: 'Play',
    link: 'gamemode',
    visible: toGuestPlayer
  },
  {
    name: 'Game History',
    link: 'history',
    visible: toPlayer
  },
  {
    name: 'Transactions',
    link: 'transactions',
    visible: toPlayer
  },
  {
    name: 'Scoreboard',
    link: 'scoreboard',
    visible: true
  },
  {
    name: 'Store',
    link: 'store',
    visible: toPlayer
  },
  {
    name: 'Statistics',
    link: 'statistics',
    visible: true
  },
])
</script>

<template>
  <Debug />
  <Toaster />
  <GlobalAlertDialog ref="alert-dialog"></GlobalAlertDialog>
  <GlobalInputDialog ref="input-dialog"></GlobalInputDialog>

  <div class="md:bg-gray-100 min-h-screen flex flex-col">
    <Navbar :menu-items="navigation" />
    <div class="container flex-grow flex justify-center h-full bg-white shadow xl:my-3 xl:rounded-xl py-5">
      <router-view />
    </div>
    <Footer></Footer>
  </div>
</template>
