<template>
  <div class="flex flex-col h-full w-full justify-center">
    <Modal v-model:show="showModal" @close="showModal = false">
      <template #header>
        <h3 class="text-2xl text-indigo-950">Details of Game #{{ currentGame.id }}</h3>
      </template>
      <template #body>
        <div class="border rounded-md">
          <Table class="text-base">
            <!-- <pre>{{ currentGame }}</pre> -->
            <TableBody>
              <TableRow>
                <TableHead>Game ID</TableHead>
                <TableCell>{{ currentGame.id }}</TableCell>
              </TableRow>
              <TableRow>
                <TableHead>Type</TableHead>
                <TableCell>{{ currentGame.type }}</TableCell>
              </TableRow>
              <TableRow>
                <TableHead>Status</TableHead>
                <TableCell>
                  <Badge class="place-self-start" :variant="currentGame.status === 'Ended' ? 'default-outline' : currentGame.status === 'Pending' ? 'indigo-outline' : currentGame.status === 'In Progress' ? 'yellow-outline' : 'red-outline'">
                    {{ currentGame.status }}
                  </Badge>
                </TableCell>
              </TableRow>
              <TableRow v-if="currentGame.start_date">
                <TableHead>Start Date</TableHead>
                <TableCell>{{ currentGame.start_date }}</TableCell>
              </TableRow>
              <TableRow v-if="currentGame.start_time">
                <TableHead>Start Time</TableHead>
                <TableCell>{{ currentGame.start_time }}</TableCell>
              </TableRow>
              <TableRow v-if="currentGame.end_date">
                <TableHead>End Date</TableHead>
                <TableCell>{{ currentGame.end_date }}</TableCell>
              </TableRow>
              <TableRow v-if="currentGame.end_time">
                <TableHead>End Time</TableHead>
                <TableCell>{{ currentGame.end_time }}</TableCell>
              </TableRow>
              <TableRow v-if="currentGame.opponent_user_id">
                <TableHead>Opponent</TableHead>
                <TableCell class="flex flex-row items-center gap-3">
                  <img :src="storeAuth.getPhotoURL(currentGame.opponent_user_id?.photoFileName)" class="h-10 w-10 rounded-full" />
                  {{ currentGame.opponent_user_id?.name ?? 'Unknown Player' }}
                </TableCell>
              </TableRow>
              <TableRow v-if="currentGame.created_user_id && (currentGame.type=='Multiplayer' || storeAuth.isAdmin) ">
                <TableHead>Created by</TableHead>
                <TableCell class="flex flex-row items-center gap-3">
                  <img :src="storeAuth.getPhotoURL(currentGame.created_user_id?.photoFileName)" class="h-10 w-10 rounded-full" />
                  {{ currentGame.created_user_id?.name }}
                </TableCell>
              </TableRow>
              <TableRow v-if="currentGame.winner_user_id">
                <TableHead>Winner</TableHead>
                <TableCell class="flex flex-row items-center gap-3">
                  <img :src="storeAuth.getPhotoURL(currentGame.winner_user_id?.photoFileName)" class="h-10 w-10 rounded-full" />
                  {{ currentGame.winner_user_id?.name }}
                </TableCell>
              </TableRow>
              <TableRow v-if="currentGame.board.name">
                <TableHead>Board</TableHead>
                <TableCell>{{ currentGame.board.name }}</TableCell>
              </TableRow>
              <TableRow v-if="currentGame.total_time">
                <TableHead>Total Time</TableHead>
                <TableCell>{{ currentGame.total_time }} s</TableCell>
              </TableRow>
              <TableRow v-if="currentGame.turns">
                <TableHead>Turns</TableHead>
                <TableCell>{{ currentGame.turns }}</TableCell>
              </TableRow>
            </TableBody>
          </Table>
        </div>
      </template>
    </Modal>

    <div class="space-y-4 mt-5" v-if="storeGame.games">
      <div class="flex gap-4 flex-col md:flex-row">
        <Select v-model="selectedType" @update:modelValue="handleFiltersChange">
          <SelectTrigger class="w-[180px]">
            <SelectValue placeholder="Select type" />
          </SelectTrigger>
          <SelectContent>
            <SelectItem v-if="selectedType">None</SelectItem>
            <SelectItem value="S">Single Player</SelectItem>
            <SelectItem value="M">Multiplayer</SelectItem>
          </SelectContent>
        </Select>

        <Select v-model="selectedStatus" @update:modelValue="handleFiltersChange">
          <SelectTrigger class="w-[180px]">
            <SelectValue placeholder="Select status" />
          </SelectTrigger>
          <SelectContent>
            <SelectItem v-if="selectedStatus">None</SelectItem>
            <SelectItem value="PE">Pending</SelectItem>
            <SelectItem value="PL">Playing</SelectItem>
            <SelectItem value="E">Ended</SelectItem>
            <SelectItem value="I">Interrupted</SelectItem>
          </SelectContent>
        </Select>

        <Select v-model="selectedBoard" @update:modelValue="handleFiltersChange">
          <SelectTrigger class="w-[180px]">
            <SelectValue placeholder="Select Board" />
          </SelectTrigger>
          <SelectContent>
            <SelectItem v-if="selectedBoard">None</SelectItem>
            <SelectItem value="1">3x4</SelectItem>
            <SelectItem value="2">4x4</SelectItem>
            <SelectItem value="3">6x6</SelectItem>
          </SelectContent>
        </Select>
      </div>

      <div class="rounded-md border">
        <Table>
          <TableHeader>
            <TableRow>
              <TableHead>
                <div class="flex items-center gap-2 cursor-pointer" @click="toggleSort('id')">
                  ID
                  <ArrowUpDown class="h-4 w-4" />
                </div>
              </TableHead>
              <TableHead>Type</TableHead>
              <TableHead>Status</TableHead>
              <TableHead>
                <div class="flex items-center gap-2 cursor-pointer" @click="toggleSort('total_time')">
                  Total Time
                  <ArrowUpDown class="h-4 w-4" />
                </div>
              </TableHead>
              <TableHead>
                <div class="flex items-center gap-2 cursor-pointer" @click="toggleSort('turns')">
                  Turns
                  <ArrowUpDown class="h-4 w-4" />
                </div>
              </TableHead>
              <TableHead>
                <div class="flex items-center gap-2 cursor-pointer" @click="toggleSort('began_at')">
                  Start Date
                  <ArrowUpDown class="h-4 w-4" />
                </div>
              </TableHead>
              <TableHead>Start Time</TableHead>
              <TableHead>End Date</TableHead>
              <TableHead>End Time</TableHead>
              <TableHead>Board</TableHead>
              <TableHead>See More</TableHead>
            </TableRow>
          </TableHeader>
          <TableBody>
            <TableRow v-for="game in storeGame.games" :key="game.id">
              <TableCell>{{ game.id }}</TableCell>
              <TableCell class="text-nowrap">
                <Badge :variant="game.type === 'Single-player' ? 'green' : 'blue'">
                  {{ game.type }}
                </Badge>
              </TableCell>
              <TableCell class="text-nowrap">
                <Badge :variant="game.status === 'Ended' ? 'default-outline' : game.status === 'Pending' ? 'indigo-outline' : game.status === 'In Progress' ? 'yellow-outline' : 'red-outline'">
                  {{ game.status }}
                </Badge>
              </TableCell>
              <TableCell>{{ game.total_time ? game.total_time + ' s' : '-' }}</TableCell>
              <TableCell>{{ game.turns ?? '-' }}</TableCell>
              <TableCell>{{ game.start_date ?? '-' }}</TableCell>
              <TableCell>{{ game.start_time ?? '-' }}</TableCell>
              <TableCell>{{ game.end_date ?? '-' }}</TableCell>
              <TableCell>{{ game.end_time ?? '-' }}</TableCell>
              <TableCell>{{ game.board.name }}</TableCell>
              <TableCell>
                <ExternalLinkIcon @click="showGame(game)" class="stroke-1 w-auto place-self-center cursor-pointer hover:text-indigo-500" />
              </TableCell>
            </TableRow>
            <TableRow v-if="!storeGame.games?.length">
              <TableCell colspan="11" class="text-center h-24">No Games found</TableCell>
            </TableRow>
          </TableBody>
        </Table>
      </div>
      <div v-if="storeGame.games.length < storeGame.totalGames" class="flex justify-center">
        <Button variant="outline" @click="loadMore" :disabled="loading">
          <Loader2 v-if="loading" class="mr-2 h-4 w-4 animate-spin" />
          Load More
        </Button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Input } from '@/components/ui/input'
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select'
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table'
import { useUserStore } from '@/stores/user'
import { useAuthStore } from '@/stores/auth'
import { ArrowUpDown, ExternalLinkIcon, Loader2, Plus, Search } from 'lucide-vue-next'
import { onMounted, ref } from 'vue'
import { Badge } from '../ui/badge'
import Button from '../ui/button/Button.vue'
import { useRouter } from 'vue-router'
import { useGameStore } from '@/stores/game'
import Modal from '../ui/modal/Modal.vue'

const router = useRouter()
const storeGame = useGameStore()
const storeAuth = useAuthStore()

const loading = ref(false)
const selectedType = ref('')
const selectedStatus = ref('')
const sortField = ref('id')
const sortDirection = ref('desc')
const selectedBoard = ref('')

const currentGame = ref({})
const showModal = ref(false)

const loadMore = async () => {
  loading.value = true
  await storeGame.fetchGamesNextPage()
  loading.value = false
}

const handleFiltersChange = async () => {
  await fetchData(true)
}

const toggleSort = (field) => {
  if (sortField.value === field) {
    sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc'
  } else {
    sortField.value = field
    sortDirection.value = 'desc'
  }
  handleFiltersChange()
}

const fetchData = async (resetPagination = false) => {
  loading.value = true

  storeGame.filters = {
    type: selectedType.value,
    status: selectedStatus.value,
    board: selectedBoard.value,
    sort_by: sortField.value,
    sort_direction: sortDirection.value
  }

  await storeGame.fetchGames(resetPagination)
  loading.value = false
}

const showGame = (game) => {
  currentGame.value = game
  showModal.value = true
}

onMounted(async () => {
  fetchData()
})
</script>
