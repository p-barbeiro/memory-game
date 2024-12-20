<template>
  <div class="place-self-start w-full">
    <div class="rounded-full flex justify-center mb-3">
      <button class="border border-collapse rounded-l-full w-52 text-sm p-2 hover:bg-indigo-100" :class="{ 'bg-indigo-100 text-indigo-950 border-indigo-200': toggle }" @click="toggle = 1">Personal Scoreboard</button>
      <button class="border border-collapse rounded-r-full text-sm w-52 p-2 hover:bg-indigo-100" :class="{ 'bg-indigo-100 text-indigo-950 border-indigo-200': !toggle }" @click="toggle = 0">Global Scoreboard</button>
    </div>
    <hr class="my-5" />

    <div class="w-full flex justify-center">
      <div v-if="toggle" class="w-full">
        <span class="flex justify-center font-semibold"> Single-Player Scoreboard </span>
        <hr class="my-5" />

        <span class="text-sm text-gray-800 border p-2 rounded-md border-gray-100"> Highscore by <b>Time</b>: </span>

        <div class="my-3 grid md:grid-cols-3 gap-3">
          <div class="border rounded-md" v-for="(board, boardSize) in personalScoreboard.single_player_top_3_by_time_board" :key="boardSize">
            <Table>
              <TableHeader class="bg-gray-50">
                <TableRow>
                  <TableHead colspan="2" class="text-center text-lg font-semibold text-indigo-950">{{ boardSize }}</TableHead>
                </TableRow>
              </TableHeader>
              <TableBody>
                <TableRow v-for="(game, index) in board" :key="index">
                  <TableHead class="w-1/2 text-right">{{ index + 1 }}º Best Time</TableHead>
                  <TableCell class="w-1/2 text-left">{{ game.total_time }} s</TableCell>
                </TableRow>
              </TableBody>
            </Table>
          </div>
        </div>

        <hr class="my-5" />

        <span class="text-sm text-gray-800 border p-2 rounded-md border-gray-100"> Highscore by <b>Turns</b>: </span>

        <div class="mt-3 grid md:grid-cols-3 gap-3">
          <div class="border rounded-md" v-for="(board, boardSize) in personalScoreboard.single_player_top_3_by_turns_board" :key="boardSize">
            <Table>
              <TableHeader class="bg-gray-50">
                <TableRow>
                  <TableHead colspan="2" class="text-center text-lg font-semibold text-indigo-950"> {{ boardSize }}</TableHead>
                </TableRow>
              </TableHeader>
              <TableBody>
                <TableRow v-for="(game, index) in board" :key="index">
                  <TableHead class="w-1/2 text-right">{{ index + 1 }}º Best Turns</TableHead>
                  <TableCell class="w-1/2 text-left">{{ game.total_turns_winner }} turns</TableCell>
                </TableRow>
              </TableBody>
            </Table>
          </div>
        </div>

        <hr class="my-5" />

        <span class="flex justify-center font-semibold"> Multiplayer Scoreboard </span>
        <hr class="my-5" />

        <div class="grid md:grid-cols-3 gap-3">
          <div class="border rounded-md" v-for="(board, boardSize) in personalScoreboard.multiplayer_total_victories_and_losses" :key="boardSize">
            <Table>
              <TableHeader class="bg-gray-50">
                <TableRow>
                  <TableHead colspan="2" class="text-center text-lg font-semibold text-indigo-950"> {{ boardSize }}</TableHead>
                </TableRow>
              </TableHeader>
              <TableBody>
                <TableRow class="bg-green-50">
                  <TableHead class="w-1/2 text-right">Total Victories</TableHead>
                  <TableCell class="w-1/2 text-left">{{ board.total_victories }}</TableCell>
                </TableRow>
                <TableRow class="bg-red-50">
                  <TableHead class="w-1/2 text-right">Total Losses</TableHead>
                  <TableCell class="w-1/2 text-left">{{ board.total_losses }}</TableCell>
                </TableRow>
              </TableBody>
            </Table>
          </div>
        </div>
      </div>
      <div v-else class="w-full">
        <span class="flex justify-center font-semibold"> Single-Player Global Scoreboard </span>
        <hr class="my-5" />

        <!-- Highscore by Best Time -->
        <span class="text-sm text-gray-800 border p-2 rounded-md border-gray-100"> Highscore by <b>Time</b>: </span>

        <div class="my-3 grid md:grid-cols-3 gap-3">
          <div class="border rounded-md" v-for="(board, boardSize) in globalScoreboard.single_player_best_time" :key="boardSize">
            <Table>
              <TableHeader class="bg-gray-50">
                <TableRow>
                  <TableHead colspan="2" class="text-center text-lg font-semibold text-indigo-950">
                    {{ boardSize }}
                  </TableHead>
                </TableRow>
              </TableHeader>
              <TableBody>
                <TableRow>
                  <TableHead class="w-1/2 text-right">User</TableHead>
                  <TableCell class="w-1/2 text-left">
                    <div class="flex items-center gap-2">
                      <img :src="storeAuth.getPhotoURL(board.user.photoFileName)" class="w-10 h-10 rounded-full" />
                      <span class="font-semibold">{{ board.user.nickname }}</span>
                    </div>
                  </TableCell>
                </TableRow>
                <TableRow>
                  <TableHead class="w-1/2 text-right">Best Time</TableHead>
                  <TableCell class="w-1/2 text-left">{{ board.best_time }} s</TableCell>
                </TableRow>
              </TableBody>
            </Table>
          </div>
        </div>

        <hr class="my-5" />

        <!-- Highscore by Min Turns -->
        <span class="text-sm text-gray-800 border p-2 rounded-md border-gray-100"> Highscore by <b>Turns</b>: </span>

        <div class="mt-3 grid md:grid-cols-3 gap-3">
          <div class="border rounded-md" v-for="(board, boardSize) in globalScoreboard.single_player_min_turns" :key="boardSize">
            <Table>
              <TableHeader class="bg-gray-50">
                <TableRow>
                  <TableHead colspan="2" class="text-center text-lg font-semibold text-indigo-950">
                    {{ boardSize }}
                  </TableHead>
                </TableRow>
              </TableHeader>
              <TableBody>
                <TableRow>
                  <TableHead class="w-1/2 text-right">User</TableHead>
                  <TableCell class="w-1/2 text-left"> 
                    <div class="flex items-center gap-2">
                      <img :src="storeAuth.getPhotoURL(board.user.photoFileName)" class="w-10 h-10 rounded-full" />
                      <span class="font-semibold">{{ board.user.nickname }}</span>
                    </div>
                  </TableCell>
                </TableRow>
                <TableRow>
                  <TableHead class="w-1/2 text-right">Min. Turns</TableHead>
                  <TableCell class="w-1/2 text-left">{{ board.min_turns }}</TableCell>
                </TableRow>
              </TableBody>
            </Table>
          </div>
        </div>

        <hr class="my-5" />

        <span class="flex justify-center font-semibold"> Multiplayer Global Scoreboard </span>
        <hr class="my-5" />

        <!-- Multiplayer Top Players -->
        <div>
          <div class="border rounded-md">
            <Table>
              <TableHeader class="bg-gray-50">
                <TableRow>
                  <TableHead colspan="4" class="text-center text-lg font-semibold text-indigo-950"> Top 5 Players </TableHead>
                </TableRow>
              </TableHeader>
              <TableBody>
                <TableRow class="flex justify-center bg-green-50" v-for="player in globalScoreboard.multiplayer_top_players" :key="player">
                  <TableCell>
                    <img :src="storeAuth.getPhotoURL(player.user.photoFileName)" class="w-10 h-10 rounded-full" />
                  </TableCell>
                  <TableCell>
                    <div class="flex items-center gap-2">
                      <span class="font-semibold">{{ player.user.name }}</span>
                    </div>
                    {{ player.user.nickname }}
                  </TableCell>
                  <TableCell class="ml-auto items-center flex"
                    >Victories: <b>{{ player.total_victories }}</b></TableCell
                  >
                </TableRow>
              </TableBody>
            </Table>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useAuthStore } from '@/stores/auth'
import { useErrorStore } from '@/stores/error'
import axios from 'axios'
import { onMounted, ref } from 'vue'
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table'

const storeError = useErrorStore()
const storeAuth = useAuthStore()
const toggle = ref(1)

const personalScoreboard = ref({})
const globalScoreboard = ref({})
const isLoading = ref(false)

const fetchPersonalHighscore = async () => {
  storeError.resetMessages()
  try {
    isLoading.value = true
    axios.defaults.headers.common.Authorization = 'Bearer ' + storeAuth.token
    axios.defaults.headers.common['Content-Type'] = 'application/json'

    const response = await axios.get('scoreboards/personal')

    if (response.status !== 200) {
      throw response
    }

    personalScoreboard.value = response.data
    isLoading.value = false
  } catch (e) {
    storeError.setErrorMessages(e.response.data.message, e.response.data.errors, e.response.status, 'Error fetching game!')
    isLoading.value = false
    return false
  }
}
const fetchGlobalHighscore = async () => {
  storeError.resetMessages()
  try {
    isLoading.value = true
    axios.defaults.headers.common.Authorization = 'Bearer ' + storeAuth.token
    axios.defaults.headers.common['Content-Type'] = 'application/json'

    const response = await axios.get('scoreboards/global')

    if (response.status !== 200) {
      throw response
    }

    globalScoreboard.value = response.data
    isLoading.value = false
  } catch (e) {
    storeError.setErrorMessages(e.response.data.message, e.response.data.errors, e.response.status, 'Error fetching game!')
    isLoading.value = false
    return false
  }
}

onMounted(async () => {
  await fetchPersonalHighscore()
  await fetchGlobalHighscore()
})
</script>
