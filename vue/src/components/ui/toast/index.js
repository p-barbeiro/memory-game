export { default as Toast } from './Toast.vue'
export { default as ToastAction } from './ToastAction.vue'
export { default as ToastClose } from './ToastClose.vue'
export { default as ToastDescription } from './ToastDescription.vue'
export { default as Toaster } from './Toaster.vue'
export { default as ToastProvider } from './ToastProvider.vue'
export { default as ToastTitle } from './ToastTitle.vue'
export { default as ToastViewport } from './ToastViewport.vue'
export { toast, useToast } from './use-toast'

import { cva } from 'class-variance-authority'

export const toastVariants = cva(
  'group pointer-events-auto relative flex w-full items-center justify-between space-x-4 overflow-hidden rounded-md border p-6 pr-8 shadow-lg transition-all data-[swipe=cancel]:translate-x-0 data-[swipe=end]:translate-x-[--radix-toast-swipe-end-x] data-[swipe=move]:translate-x-[--radix-toast-swipe-move-x] data-[swipe=move]:transition-none data-[state=open]:animate-in data-[state=closed]:animate-out data-[swipe=end]:animate-out data-[state=closed]:fade-out-80 data-[state=closed]:slide-out-to-right-full data-[state=open]:slide-in-from-top-full data-[state=open]:sm:slide-in-from-bottom-full',
  {
    variants: {
      variant: {
        default: 'bg-gray-200 border border-gray-300 text-sm text-gray-800 rounded-lg',
        destructive: 'bg-red-100 border border-red-200 text-sm text-red-800 rounded-lg',
        success: 'bg-teal-100 border border-teal-200 text-sm text-teal-800 rounded-lg',
        info: 'bg-blue-100 border border-blue-200 text-sm text-blue-800 rounded-lg',
        warning: 'bg-yellow-100 border border-yellow-200 text-sm text-yellow-800 rounded-lg',
        light: 'bg-gray-50 border border-gray-200 text-sm text-gray-600 rounded-lg',
      }
    },
    defaultVariants: {
      variant: 'default'
    }
  }
)

 
