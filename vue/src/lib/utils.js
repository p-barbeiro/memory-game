import { clsx } from "clsx";
import { twMerge } from "tailwind-merge";
import { ref } from 'vue';

export const isLoading = ref(false)

export const debugMenu = false

export function cn(...inputs) {
  return twMerge(clsx(inputs));
}

export const globalEvent = ref(new Map());