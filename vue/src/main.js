import { createApp } from 'vue'
import { createPinia } from 'pinia'
import axios from 'axios'
import { io } from 'socket.io-client'

import '@/assets/base.css'

import App from './App.vue'
import router from './router'

import ErrorMessage from './components/common/ErrorMessage.vue'

const app = createApp(App)

app.use(router)

app.use(createPinia())
const apiDomain = import.meta.env.VITE_API_DOMAIN
const wsConnection = import.meta.env.VITE_WS_CONNECTION

console.log('api domain', apiDomain)
console.log('ws connection', wsConnection)

axios.defaults.baseURL = `http://${apiDomain}/api`

app.provide('socket', io(wsConnection))
app.provide('serverBaseUrl', apiDomain)

app.component('ErrorMessage', ErrorMessage)

app.mount('#app')

