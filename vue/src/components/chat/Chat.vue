<script setup>
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { inject, ref } from 'vue'
import { Icon } from '@iconify/vue'

import { useAuthStore } from '@/stores/auth'
import { useChatStore } from '@/stores/chat'

const storeChat = useChatStore()
const storeAuth = useAuthStore()

const inputDialog = inject('inputDialog')

const message = ref('')

const canSendMessageToUser = (user) => {
  return user && storeAuth.user && user.id !== storeAuth.user.id
}

const sendMessageToChat = () => {
  if (message.value.trim()) {
    storeChat.sendMessageToChat(message.value)
    message.value = ''
  }
}

// Define a set of colors
const textColors = ['text-red-500', 'text-blue-500', 'text-green-500', 'text-yellow-500', 'text-purple-500', 'text-pink-500', 'text-orange-500', 'text-cyan-500']

// Function to hash user ID to an index for consistent color assignment
const getUserTextColor = (userId) => {
  if (!userId) return 'text-gray-500' // Default color for anonymous users
  const hash = Array.from(userId.toString()).reduce((acc, char) => acc + char.charCodeAt(0), 0)
  return textColors[hash % textColors.length]
}
</script>

<template>
  <Card>
    <CardHeader>
      <CardTitle>Chat</CardTitle>
    </CardHeader>

    <CardContent>
      <div v-if="storeChat.totalMessages > 0" class="border rounded-md bg-gray-50 border-indigo-950/50 p-0.5 min-h-60 flex flex-col justify-end">
        <div v-for="messageObj in storeChat.messages" :key="messageObj" class="rounded flex flex-row text-sm bg-gray-50 border-b p-1 h-6" :class="{ 'bg-indigo-50': messageObj.user?.nickname == storeAuth.user.nickname }">
          <div class="flex flex-row grow items-center">
            <div class="font-semibold" :class="getUserTextColor(messageObj.user?.id)">
              {{ messageObj.user?.nickname ?? 'Anonymous' }}
            </div>
            <div class="ml-1">
              {{ messageObj.message }}
            </div>
          </div>
        </div>
      </div>
      <div v-else class="h-60 flex text-sm bg-gray-50 border border-indigo-950/50 p-1 place-items-end">
        <div class="ml-1">No messages yet! Be the first to send a message!</div>
      </div>
      <div class="w-full flex flex-col h-full">
        <div class="flex flex-row items-center mt-1 border border-indigo-950/50 bg-white h-10 rounded-md">
          <Input id="inputMessage" v-model="message" @keydown.enter="sendMessageToChat" class="h-full rounded-md border-none focus-visible:ring-0" placeholder="Type your message" />
          <Icon icon="proicons:send" width="24" height="24" class="h-full mx-2 cursor-pointer" @click="sendMessageToChat" />
        </div>
      </div>
    </CardContent>
  </Card>
</template>
