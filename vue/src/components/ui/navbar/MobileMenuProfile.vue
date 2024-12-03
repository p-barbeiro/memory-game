<template>
	<li class="ml-2 cursor-pointer text-gray-600 text-sm leading-3 tracking-normal py-2 hover:text-blue-900 flex items-center focus:text-blue-900 focus:outline-none">
	      <div class="flex items-center">
	        <div class="w-16 cursor-pointer flex text-sm border-2 border-transparent rounded focus:outline-none focus:border-white transition duration-150 ease-in-out">
	          <img class="rounded h-16 w-16 object-cover border" :src="auth.userPhotoUrl" alt="logo" />
	        </div>
	        <p class="leading-6 text-base ml-1 cursor-pointer">{{ auth.userName }}</p>
	      </div>
	    </li>
</template>

<script setup>
import { useAuthStore } from '@/stores/auth';

const auth = useAuthStore()

</script>
