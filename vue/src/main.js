import axios from 'axios'
import { createPinia } from 'pinia'
import { io } from 'socket.io-client'
import { createApp } from 'vue'

import '@/assets/base.css'

import App from './App.vue'
import router from './router'

import ErrorMessage from './components/common/ErrorMessage.vue'


const app = createApp(App)
const apiDomain = import.meta.env.VITE_API_DOMAIN
console.log('api domain', apiDomain)
const wsConnection = import.meta.env.VITE_WS_CONNECTION
console.log('ws connection', wsConnection)

app.use(router)
app.use(createPinia())

axios.defaults.baseURL = `http://${apiDomain}/api`
app.provide('socket', io(wsConnection))
app.provide('serverBaseUrl', apiDomain)

app.component('ErrorMessage', ErrorMessage)

app.mount('#app')

