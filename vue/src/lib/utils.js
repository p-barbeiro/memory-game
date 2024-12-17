import { clsx } from "clsx";
import { twMerge } from "tailwind-merge";
import { ref } from 'vue';

export const isLoading = ref(false)

export const debugMenu = false

export function cn(...inputs) {
  return twMerge(clsx(inputs));
}

export function toProperCase(str) {
  return str
      .toLowerCase()  // First, make the string lowercase
      .split(' ')     // Split the string into words
      .map(word => {
          return word.charAt(0).toUpperCase() + word.slice(1);  // Capitalize the first letter of each word
      })
      .join(' ');      // Join the words back together
}

export const globalEvent = ref(new Map());