<script setup>
import { ref } from 'vue'

import { AlertDialog, AlertDialogAction, AlertDialogCancel, AlertDialogContent, AlertDialogDescription, AlertDialogFooter, AlertDialogHeader, AlertDialogTitle } from '@/components/ui/alert-dialog'

const isOpen = ref(false)
const titleText = ref('Title')
const descriptionText = ref('')
const cancelText = ref('Cancel')
const actionText = ref('OK')
const actionCallBack = ref(null)
const cancelCallBack = ref(null)

const open = (actionCallBackFunction, title = 'Title', cancelLabel = 'Cancel', actionLabel = 'Continuar', description = '', cancelActionCallBackFunction = null) => {
  titleText.value = title
  descriptionText.value = description
  cancelText.value = cancelLabel
  actionText.value = actionLabel
  actionCallBack.value = actionCallBackFunction
  cancelCallBack.value = cancelActionCallBackFunction
  isOpen.value = true
}

const handleAction = () => {
  if (actionCallBack.value) {
    actionCallBack.value()
  }
}

const handleCancel = () => {
  if (cancelCallBack.value) {
    cancelCallBack.value()
  }
}

defineExpose({
  open
})
</script>

<template>
  <AlertDialog v-model:open="isOpen">
    <AlertDialogContent>
      <AlertDialogHeader>
        <AlertDialogTitle>{{ titleText }}</AlertDialogTitle>
        <AlertDialogDescription>
          <span v-html="descriptionText.replace(/\n/g, '<br>')"></span>
        </AlertDialogDescription>
      </AlertDialogHeader>
      <AlertDialogFooter>
        <AlertDialogCancel v-if="cancelText" @click="handleCancel">{{ cancelText }}</AlertDialogCancel>
        <AlertDialogAction @click="handleAction">{{ actionText }}</AlertDialogAction>
      </AlertDialogFooter>
    </AlertDialogContent>
  </AlertDialog>
</template>
