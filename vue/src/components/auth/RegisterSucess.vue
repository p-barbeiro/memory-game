<script setup>
import { Button } from '@/components/ui/button'
import { Card, CardContent, CardFooter, CardHeader, CardTitle } from '@/components/ui/card'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { useAuthStore } from '@/stores/auth'
import { useErrorStore } from '@/stores/error'
import { useRouter } from 'vue-router'
import IconPhoto from '../icons/IconPhoto.vue'

const router = useRouter()
const storeError = useErrorStore()
const storeAuth = useAuthStore()
</script>

<template>
    <div class="place-self-center flex flex-col justify-center items-center w-full h-full">
      <h2 class="text-2xl text-center">Account successfully created!</h2>
      <img class="w-1/3" src="/src/assets/img/register_success.jpg" alt="Register Sucess" />
      <div class="flex flex-col gap-5 text-center">
        Your journey to becoming a memory master starts now - Have fun!
        <Button @click="router.push({ name: 'gamemode' })">Play</Button>
      </div>
    </div>
</template>
