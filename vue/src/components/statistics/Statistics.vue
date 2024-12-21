<template>
  <div v-if="!apiData" class="place-self-center">
    <IconLoading :size="32" />
  </div>
  <div v-else class="w-full">
    <div class="w-full rounded-xl bg-gradient-to-b from-indigo-950 to-indigo-800 h-96">
      <div class="mx-auto container w-full flex flex-col justify-center items-center">
        <div class="flex justify-center items-center flex-col">
          <div class="mt-20">
            <h2 class="lg:text-6xl md:text-5xl text-4xl font-black leading-10 text-white">By the numbers</h2>
          </div>
          <div class="mt-6 mx-2 md:mx-0 text-center">
            <p class="lg:text-lg md:text-base leading-6 text-sm text-white">Sharpen Your Mind: Explore the Stats Behind the Game!</p>
          </div>
        </div>
      </div>
    </div>
    <div class="mx-auto container md:-mt-28 -mt-20 flex justify-center items-center">
      <div class="grid lg:grid-cols-6 md:grid-cols-2 grid-cols-2 gap-x-2 gap-y-2 lg:gap-x-2 md:gap-x-6 md:gap-y-6">
        <StatisticCard desc="Players" :data="apiData.totalPlayers" />
        <StatisticCard desc="Games Played" :data="apiData.totalGamesPlayed" />
        <StatisticCard desc="Games Last Week" :data="apiData.gamesPlayedLastWeek" />
        <StatisticCard desc="Games Last Month" :data="apiData.gamesPlayedLastMonth" />
        <StatisticCard desc="Single-Player Games" :data="apiData.totalSinglePlayerGames" />
        <StatisticCard desc="Multiplayer Games" :data="apiData.totalMultiplayerGames" />
      </div>
    </div>
    <div class="mt-5">
      <Card class="h-full p-5 text-justify justify-center items-center flex flex-col text-gray-600">
        <span class="flex justify-center"> A collaborative academic project by Diogo Abegão, João Parreira, Marcelo Oliveira and Pedro Barbeiro. </span>
        <br />
        <span class="flex justify-center">
          This academic project focuses on the development of a fully functional single-page application (SPA) for the 'Memory Game' platform. The web client is built using the Vue.js framework, leveraging its powerful features for creating a dynamic and responsive user interface. A RESTful API server is implemented to manage the game's data, ensuring efficient communication between the client and server. Additionally, a WebSocket server is integrated to provide real-time interactions, enabling
          smooth and instant updates during gameplay.
        </span>
        <br />
        <span class="flex justify-center"> Developed as part of the 'Distributed Applications Development' course at the Polytechnic Institute of Leiria. </span>
        <br>
        <span class="flex justify-center"> 1st Semester | 2024 - 2025 </span>
      </Card>
    </div>
  </div>
</template>

<script setup>
import { useAuthStore } from '@/stores/auth'
import { useErrorStore } from '@/stores/error'
import { onMounted, ref } from 'vue'
import IconLoading from '../icons/IconLoading.vue'
import StatisticCard from './StatisticCard.vue'
import axios from 'axios'
import Card from '../ui/card/Card.vue'
import CardHeader from '../ui/card/CardHeader.vue'

const storeAuth = useAuthStore()
const storeError = useErrorStore()
const isLoading = ref(false)
const apiData = ref(null)

const fetchGlobalStatistics = async () => {
  storeError.resetMessages()
  try {
    isLoading.value = true
    axios.defaults.headers.common.Authorization = 'Bearer ' + storeAuth.token
    axios.defaults.headers.common['Content-Type'] = 'application/json'

    const response = await axios.get(`statistics/global`)

    if (response.status !== 200) {
      throw response
    }

    apiData.value = response.data
    isLoading.value = false
  } catch (e) {
    storeError.setErrorMessages(e.response.data.message, e.response.data.errors, e.response.status, 'Error fetching personal statistics!')
    isLoading.value = false
    return false
  }
}

onMounted(async () => {
  isLoading.value = true
  await fetchGlobalStatistics()
  isLoading.value = false
})
</script>
