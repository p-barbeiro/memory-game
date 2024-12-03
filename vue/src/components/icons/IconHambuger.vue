<template>
  <svg @click="MenuHandler($event, true)" aria-haspopup="true" aria-label="Main Menu" class="show-m-menu icon icon-tabler icon-tabler-menu" width="28" height="28" viewBox="0 0 24 24" stroke-width="1.5" stroke="#000" fill="none" stroke-linecap="round" stroke-linejoin="round">
    <path stroke="none" d="M0 0h24v24H0z"></path>
    <line x1="4" y1="5" x2="20" y2="5"></line>
    <line x1="4" y1="10" x2="20" y2="10"></line>
    <line x1="4" y1="15" x2="20" y2="15"></line>
  </svg>
  <div class="hidden close-m-menu" @click="MenuHandler($event, false)">
    <svg aria-label="Close" width="24" height="24" viewBox="0 0 24 24" stroke-width="1.5" stroke="#000" fill="none" stroke-linecap="round" stroke-linejoin="round">
      <path stroke="none" d="M0 0h24v24H0z" />
      <line x1="18" y1="6" x2="6" y2="18" />
      <line x1="6" y1="6" x2="18" y2="18" />
    </svg>
  </div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount, watch } from 'vue'
import { globalEvent } from '@/lib/utils';

function MenuHandler(el, val) {
  let MainList = el.currentTarget.parentElement.getElementsByTagName('ul')[0]
  let closeIcon = el.currentTarget.parentElement.getElementsByClassName('close-m-menu')[0]
  let showIcon = el.currentTarget.parentElement.getElementsByClassName('show-m-menu')[0]
  if (val) {
    MainList.classList.remove('hidden')
    el.currentTarget.classList.add('hidden')
    closeIcon.classList.remove('hidden')
  } else {
    showIcon.classList.remove('hidden')
    MainList.classList.add('hidden')
    el.currentTarget.classList.add('hidden')
  }
}

function closeMenu() {
  let MainList = document.querySelector('.show-m-menu').parentElement.getElementsByTagName('ul')[0]
  let closeIcon = document.querySelector('.close-m-menu')
  let showIcon = document.querySelector('.show-m-menu')
  showIcon.classList.remove('hidden')
  MainList.classList.add('hidden')
  closeIcon.classList.add('hidden')
}

onMounted(() => {
  globalEvent.value.set('closeMenu', false)
  watch(() => globalEvent.value.get('closeMenu'), (newValue) => {
    if (newValue) {
      closeMenu()
      globalEvent.value.set('closeMenu', false)
    }
  })
})

onBeforeUnmount(() => {
  globalEvent.value.delete('closeMenu')
})
</script>