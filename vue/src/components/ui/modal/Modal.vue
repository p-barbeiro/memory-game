<script setup>
import { X } from 'lucide-vue-next'
import { ref } from 'vue';

const props = defineProps({
  show: Boolean
})

const model = defineModel("show")

</script>

<template>
  <Transition name="modal">
    <div v-if="model" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-80 transition-opacity">
      <div class="bg-white rounded-xl shadow-lg w-full mx-5 xl:w-1/3 p-3 flex flex-col justify-around">
        <div class="flex justify-end opacity-10 cursor-pointer hover:opacity-100" @click="model=false">
          <X />
        </div>
        <div class="mb-4 font-xl">
          <slot name="header"></slot>
        </div>

        <div class="mb-4">
          <slot name="body"></slot>
        </div>

        <div class="flex justify-center">
          <slot name="footer"></slot>
        </div>
      </div>
    </div>
  </Transition>
</template>
