<template>
  <div class="flex flex-col h-full w-full justify-center">
    <Modal v-model:show="showModal" @close="showModal = false">
      <template #header>
        <h3 class="text-2xl text-indigo-950">Details of Transaction #{{ currentTransaction.id }}</h3>
      </template>
      <template #body>
        <div class="border rounded-md">
          <Table class="text-base">
            <TableBody>
              <TableRow>
                <TableHead>Transaction ID</TableHead>
                <TableCell>{{ currentTransaction.id }}</TableCell>
              </TableRow>
              <TableRow>
                <TableHead>Type</TableHead>
                <TableCell>{{ currentTransaction.type }}</TableCell>
              </TableRow>
              <TableRow>
                <TableHead>Brain Coins</TableHead>
                <TableCell>{{ currentTransaction.brain_coins }}</TableCell>
              </TableRow>
              <TableRow>
                <TableHead>Date</TableHead>
                <TableCell>{{ currentTransaction.date }}</TableCell>
              </TableRow>
              <TableRow>
                <TableHead>Time</TableHead>
                <TableCell>{{ currentTransaction.time }}</TableCell>
              </TableRow>
              <TableRow v-if="currentTransaction.user && storeAuth.isAdmin">
                <TableHead>User</TableHead>
                <TableCell class="flex flex-row items-center gap-3" @click="router.push({name: 'user', params: {id: currentTransaction.user.id}})">
                  <img :src="storeAuth.getPhotoURL(currentTransaction.user?.photoFileName)" class="h-10 w-10 rounded-full" />
                  {{ currentTransaction.user?.name }}
                </TableCell>
              </TableRow>
              <TableRow v-if="currentTransaction.game_id">
                <TableHead>Game ID</TableHead>
                <TableCell> {{ currentTransaction.game_id }}</TableCell>
              </TableRow>
              <TableRow v-if="currentTransaction.euros">
                <TableHead>Amount</TableHead>
                <TableCell> {{ currentTransaction.euros }} €</TableCell>
              </TableRow>
              <TableRow v-if="currentTransaction.payment_type">
                <TableHead>Payment type</TableHead>
                <TableCell> {{ currentTransaction.payment_type }}</TableCell>
              </TableRow>
              <TableRow v-if="currentTransaction.payment_reference">
                <TableHead>Payment Reference</TableHead>
                <TableCell> {{ currentTransaction.payment_reference }}</TableCell>
              </TableRow>
              <TableRow v-if="currentTransaction.description">
                <TableHead>Description</TableHead>
                <TableCell> {{ currentTransaction.description }}</TableCell>
              </TableRow>
            </TableBody>
          </Table>
        </div>
      </template>
    </Modal>

    <div class="space-y-4 mt-5" v-if="storeTransaction.transactions">
      <div class="flex gap-4 flex-col md:flex-row">
        <Select v-model="selectedType" @update:modelValue="handleFiltersChange">
          <SelectTrigger class="md:w-[180px]">
            <SelectValue placeholder="Select Type" />
          </SelectTrigger>
          <SelectContent>
            <SelectItem v-if="selectedType">None</SelectItem>
            <SelectItem value="B">Bonus</SelectItem>
            <SelectItem value="P">Purchase</SelectItem>
            <SelectItem value="I">In-App</SelectItem>
          </SelectContent>
        </Select>

        <div v-if="storeAuth.isAdmin" class="flex w-full gap-3">
          <Input id="searc" type="text" placeholder="Search for user, email, nickname" v-model="searchFilter" class="ml-auto md:w-1/3" icon @keydown.enter="handleFiltersChange" />
          <Button class="h-10" variant="outline" @click="handleFiltersChange">
            <Search />
          </Button>
        </div>
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
              <TableHead>
                <div class="flex items-center gap-2 cursor-pointer" @click="toggleSort('brain_coins')">
                  Brain Coins
                  <ArrowUpDown class="h-4 w-4" />
                </div>
              </TableHead>
              <TableHead>
                <div class="flex items-center gap-2 cursor-pointer" @click="toggleSort('transaction_datetime')">
                  Date
                  <ArrowUpDown class="h-4 w-4" />
                </div>
              </TableHead>
              <TableHead>Time</TableHead>
              <TableHead v-if="!storeAuth.isPlayer">User</TableHead>
              <TableHead>See more</TableHead>
            </TableRow>
          </TableHeader>
          <TableBody>
            <TableRow v-for="trans in storeTransaction.transactions" :key="trans.id">
              <TableCell>{{ trans.id }}</TableCell>
              <TableCell>
                <Badge :variant="trans.type === 'Bonus' ? 'blue' : trans.type === 'Purchase' ? 'green' : 'yellow'">
                  {{ trans.type }}
                </Badge>
              </TableCell>
              <TableCell>{{ trans.brain_coins }}</TableCell>
              <TableCell>{{ trans.date }}</TableCell>
              <TableCell>{{ trans.time }}</TableCell>
              <TableCell v-if="!storeAuth.isPlayer" class="flex flex-row items-center gap-3">
                <img :src="storeAuth.getPhotoURL(trans.user?.photoFileName)" class="h-10 w-10 rounded-full" />
                {{ trans.user?.name ?? 'Unknown Player' }} </TableCell>
              <TableCell>
                <ExternalLinkIcon @click="showTransaction(trans)" class="stroke-1 w-auto place-self-center cursor-pointer hover:text-indigo-500" />
              </TableCell>
            </TableRow>
            <TableRow v-if="!storeTransaction.transactions?.length">
              <TableCell colspan="9" class="text-center h-24">No transactions found</TableCell>
            </TableRow>
          </TableBody>
        </Table>
      </div>
      <div v-if="storeTransaction.transactions.length < storeTransaction.totalTransactions" class="flex justify-center">
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
import { useTransactionStore } from '@/stores/transaction'
import Modal from '../ui/modal/Modal.vue'

const router = useRouter()
const storeTransaction = useTransactionStore()
const storeAuth = useAuthStore()

const showModal = ref(false)
const currentTransaction = ref({})

const loading = ref(false)
const selectedType = ref('')
const sortField = ref('id')
const sortDirection = ref('desc')
const searchFilter = ref('')

const loadMore = async () => {
  loading.value = true
  await storeTransaction.fetchTransactionsNextPage()
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

  storeTransaction.filters = {
    type: selectedType.value,
    search: searchFilter.value,
    sort_by: sortField.value,
    sort_direction: sortDirection.value
  }

  await storeTransaction.fetchTransactions(resetPagination)
  loading.value = false
}

const showTransaction = (transaction) => {
  currentTransaction.value = transaction
  showModal.value = true
}

onMounted(async () => {
  fetchData()
})
</script>
