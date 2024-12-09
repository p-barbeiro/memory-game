<template>
  <div v-if="debugMenu" ref="panel" class="fixed top-36 left-4 w-64 bg-white shadow-lg rounded-lg p-1 z-1000">
    <div class="flex justify-between items-center px-1 cursor-move" @mousedown="dragMouseDown">
      <h2 class="text-lg font-bold">Component Tester</h2>
      <button @click="togglePanel" class="text-gray-600">X</button>
    </div>
    <div v-if="isVisible">
      <div>
        <select v-model="selectedComponent" class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
          <option v-for="component in components" :key="component" :value="component">{{ component }}</option>
        </select>
      </div>
      <Button @click="test" class="mt-1 w-full">Test</Button>
    </div>
  </div>
</template>

<script setup>
import { debugMenu } from '@/lib/utils'
import { onBeforeUnmount, onMounted, ref } from 'vue'
import Button from '@/components/ui/button/Button.vue';

const isVisible = ref(true)
const selectedComponent = ref(null)
const components = ['Toaster', 'Modal']

function test() {
  switch (selectedComponent.value) {
    case 'Toaster':
      console.log('Toaster')
      toasterTest()
      break
    case 'Modal':
      console.log('Modal')
      modalTest()
      break
  }
}

import { toast } from '@/components/ui/toast'
const toasterTest = () => {
  const variant = ['default', 'destructive', 'success', 'info', 'warning', 'light']
  for (let i = 0; i < variant.length; i++) {
    toast({
      title: 'Title',
      description: 'Description',
      variant: variant[i]
    })
  }
}

function togglePanel() {
  isVisible.value = !isVisible.value
}

const panel = ref(null)

let pos1 = 0,
  pos2 = 0,
  pos3 = 0,
  pos4 = 0

function dragMouseDown(e) {
  e.preventDefault()
  pos3 = e.clientX
  pos4 = e.clientY
  document.onmouseup = closeDragElement
  document.onmousemove = elementDrag
}

function elementDrag(e) {
  e.preventDefault()
  pos1 = pos3 - e.clientX
  pos2 = pos4 - e.clientY
  pos3 = e.clientX
  pos4 = e.clientY
  panel.value.style.top = panel.value.offsetTop - pos2 + 'px'
  panel.value.style.left = panel.value.offsetLeft - pos1 + 'px'
}

function closeDragElement() {
  document.onmouseup = null
  document.onmousemove = null
}

onMounted(() => {
  if (panel.value) {
    panel.value.querySelector('.cursor-move').addEventListener('mousedown', dragMouseDown)
  }
})

onBeforeUnmount(() => {
  if (panel.value) {
    panel.value.querySelector('.cursor-move').removeEventListener('mousedown', dragMouseDown)
  }
})
</script>
