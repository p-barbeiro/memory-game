import { ref, computed, inject } from 'vue'
import { defineStore } from 'pinia'
import { useErrorStore } from '@/stores/error'
import { useAuthStore } from '@/stores/auth'

export const useChatStore = defineStore('chat', () => {
    const storeAuth = useAuthStore()
    const storeError = useErrorStore()

    const socket = inject('socket')
    const messages = ref([])

    const totalMessages = computed(() => messages.value.length)

    const addMessageToArray = (messageObj) => {
        if (totalMessages.value >= 10) {
            messages.value.shift();
        }
        messages.value.push(messageObj)
    }
    
    const sendMessageToChat = (message) => {
        storeError.resetMessages()
        socket.emit('chatMessage', message)
    }
    
    socket.on('chatMessage', (messageObj) => {
        addMessageToArray(messageObj)
    })

    const webSocketServerResponseHasError = (response) => {
        if (response.errorCode) {
            storeError.setErrorMessages(response.errorMessage, [], response.errorCode)
            return true
        }
        return false
    }

    const sendPrivateMessageToUser = (destinationUser, message) => {
        storeError.resetMessages()
        socket.emit(
            'privateMessage',
            {
                destinationUser: destinationUser,
                message: message
            },
            (response) => webSocketServerResponseHasError(response)
        )
    }

    return {
        messages, totalMessages, sendMessageToChat, sendPrivateMessageToUser
    }
})
