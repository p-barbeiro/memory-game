<script setup>
import { ref } from 'vue'
import { Button } from '@/components/ui/button'
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle, DialogTrigger } from '@/components/ui/dialog'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'

const isOpen = ref(false)
const titleText = ref('Title')
const descriptionText = ref('')
const mainContentText = ref('')
const cancelText = ref('Cancel')
const actionText = ref('OK')
const actionCallBack = ref(null)
const inputText = ref('')
const inputTextLabel = ref('')

const open = (actionCallBackFunction, title = 'Title', description = '', labelInputText = 'Input', initialInputText = '', cancelLabel = 'Cancel', actionLabel = 'Save', mainText = '') => {
  titleText.value = title
  inputTextLabel.value = labelInputText
  inputText.value = initialInputText
  descriptionText.value = description
  cancelText.value = cancelLabel
  actionText.value = actionLabel
  actionCallBack.value = actionCallBackFunction
  mainContentText.value = mainText
  isOpen.value = true
}

const cancel = () => {
  isOpen.value = false
}

// Only executes handle Action if the input has some text
const handleAction = () => {
  if (actionCallBack.value) {
    if (inputText.value.trim()) {
      actionCallBack.value(inputText.value)
      isOpen.value = false
    }
  }
}
defineExpose({
  open
})
</script>

<template>
  <Dialog v-model:open="isOpen">
    <DialogContent class="sm:max-w-[600px]">
      <DialogHeader>
        <DialogTitle>{{ titleText }}</DialogTitle>
        <DialogDescription>
          {{ descriptionText }}
        </DialogDescription>
      </DialogHeader>
      <div>
        <div class="text-base pb-5">
          {{ mainContentText }}
        </div>
        <div class="flex-col space-y-2">
          <Label for="inputField">
            {{ inputTextLabel }}
          </Label>
          <Input id="inputField" v-model="inputText" @keydown.enter="handleAction" />
        </div>
      </div>
      <DialogFooter class="flex justify-end space-x-3">
        <Button variant="secondary" @click="cancel" class="px-8">
          {{ cancelText }}
        </Button>
        <Button @click="handleAction" :disabled="!inputText.trim()" class="px-8">
          {{ actionText }}
        </Button>
      </DialogFooter>
    </DialogContent>
  </Dialog>
</template>
