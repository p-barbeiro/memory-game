<script setup>
import { cn } from '@/lib/utils';
import { ToastViewport } from 'radix-vue';
import { computed } from 'vue';

const props = defineProps({
  hotkey: { type: Array, required: false },
  label: { type: [String, Function], required: false },
  asChild: { type: Boolean, required: false },
  as: { type: null, required: false },
  class: { type: null, required: false },
});

const delegatedProps = computed(() => {
  const { class: _, ...delegated } = props;

  return delegated;
});
</script>

<template>
  <ToastViewport
    v-bind="delegatedProps"
    :class="
      cn(
        'fixed top-32 z-[100] gap-y-2 flex max-h-screen w-full flex-col-reverse p-4 sm:top-32 sm:right-0 sm:bottom-auto sm:flex-col md:max-w-[420px]',
        props.class,
      )
    "
  />
</template>
