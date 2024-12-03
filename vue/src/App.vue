<script setup>
import Navbar from '@/components/ui/navbar/Navbar.vue'
import GlobalAlertDialog from './components/common/GlobalAlertDialog.vue'
import Toaster from './components/ui/toast/Toaster.vue'
import { globalEvent } from './lib/utils'
import { useAuthStore } from './stores/auth'
import Footer from './components/ui/navbar/Footer.vue'
const storeAuth = useAuthStore()
import debug from './components/ui/debug.vue'

import { provide, useTemplateRef, watch } from 'vue'
const alertDialog = useTemplateRef('alert-dialog')
provide('alertDialog', alertDialog)

watch(
  () => globalEvent.value.get('logout'),
  (newValue) => {
    if (newValue) {
      alertDialog.value.open(logoutConfirmed, 'Logout confirmation?', 'Cancel', `Yes, I want to log out`, `Are you sure you want to log out? You can still access your account later with your credentials.`)
      globalEvent.value.set('logout', false)
    }
  }
)

watch(
  () => globalEvent.value.get('modalTest'),
  (newValue) => {
    if (newValue) {
      alertDialog.value.open(null, 'Logout confirmation?', 'Cancel', `Yes, I want to log out`, `Are you sure you want to log out? You can still access your account later with your credentials.`)
      globalEvent.value.set('modalTest', false)
    }
  }
)

const logoutConfirmed = () => {
  storeAuth.logout()
}
</script>

<template>
  <debug />
  <Toaster />
  <GlobalAlertDialog ref="alert-dialog"></GlobalAlertDialog>
  <div class="bg-gray-100">
    <Navbar />
    <div class="container flex justify-center items-center h-[calc(100vh-15rem)] md:h-[calc(100vh-13rem)]">
      <router-view />
    </div>
  </div>
  <Footer />
</template>
