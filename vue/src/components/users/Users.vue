<template>
  <div class="flex flex-col h-full w-full justify-center">
    <div class="space-y-4 mt-5" v-if="storeUser.users">
      <div class="flex gap-4 flex-col md:flex-row">
        <Button @click="router.push({name:'registerAdmin'})" v-if="storeAuth.isAdmin" variant="secondary" class="h-10">
          <Plus />
          Administrator
        </Button>

        <Select v-model="selectedType" @update:modelValue="handleFiltersChange">
          <SelectTrigger class="md:w-[180px]">
            <SelectValue placeholder="Select Type" />
          </SelectTrigger>
          <SelectContent>
            <SelectItem v-if="selectedType">None</SelectItem>
            <SelectItem value="A">Administrator</SelectItem>
            <SelectItem value="P">Player</SelectItem>
          </SelectContent>
        </Select>

        <Select v-model="selectedBlocked" @update:modelValue="handleFiltersChange">
          <SelectTrigger class="md:w-[180px]">
            <SelectValue placeholder="Select Blocked" />
          </SelectTrigger>
          <SelectContent>
            <SelectItem v-if="selectedBlocked">All</SelectItem>
            <SelectItem value="1">Yes</SelectItem>
            <SelectItem value="0">No</SelectItem>
          </SelectContent>
        </Select>

        <div class="flex w-full gap-3">
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
              <TableHead>Avatar</TableHead>
              <TableHead>
                <div class="flex items-center gap-2 cursor-pointer" @click="toggleSort('name')">
                  Name
                  <ArrowUpDown class="h-4 w-4" />
                </div>
              </TableHead>
              <TableHead>
                <div class="flex items-center gap-2 cursor-pointer" @click="toggleSort('nickname')">
                  Nickname
                  <ArrowUpDown class="h-4 w-4" />
                </div>
              </TableHead>
              <TableHead>
                <div class="flex items-center gap-2 cursor-pointer" @click="toggleSort('email')">
                  Email
                  <ArrowUpDown class="h-4 w-4" />
                </div>
              </TableHead>
              <TableHead>
                <div class="flex items-center gap-2 cursor-pointer" @click="toggleSort('brain_coins_balance')">
                  Brain Coins
                  <ArrowUpDown class="h-4 w-4" />
                </div>
              </TableHead>
              <TableHead>Type</TableHead>
              <TableHead>Blocked</TableHead>
              <TableHead>Open</TableHead>
            </TableRow>
          </TableHeader>
          <TableBody>
            <TableRow v-for="user in storeUser.users" :key="user.id">
              <TableCell>{{ user.id }}</TableCell>
              <TableCell>
                <img :src="storeAuth.getPhotoURL(user.photoFileName)" class="w-10 h-10 rounded-full" />
              </TableCell>
              <TableCell>{{ user.name }}</TableCell>
              <TableCell>{{ user.nickname }}</TableCell>
              <TableCell>{{ user.email }}</TableCell>
              <TableCell>{{ user.brain_coins }}</TableCell>
              <TableCell>{{ user.type }}</TableCell>
              <TableCell>
                <Badge :variant="user.blocked === true ? 'red' : 'green'">
                  {{ user.blocked === true ? 'Yes' : 'No' }}
                </Badge>
              </TableCell>
              <TableCell>
                <ExternalLinkIcon @click="router.push(user.id==storeAuth.userID ? { name: 'profile'} : { name: 'user', params: { id: user.id } })" class="stroke-1 w-auto place-self-center cursor-pointer hover:text-indigo-500" />
              </TableCell>
            </TableRow>
            <TableRow v-if="!storeUser.users?.length">
              <TableCell colspan="9" class="text-center h-24">No users found</TableCell>
            </TableRow>
          </TableBody>
        </Table>
      </div>
      <div v-if="storeUser.users.length < storeUser.totalUsers" class="flex justify-center">
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

const router = useRouter()
const storeUser = useUserStore()
const storeAuth = useAuthStore()

const loading = ref(false)
const selectedType = ref('')
const selectedBlocked = ref('')
const sortField = ref('id')
const sortDirection = ref('asc')
const searchFilter = ref('')

const loadMore = async () => {
  loading.value = true
  await storeUser.fetchUsersNextPage()
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

  storeUser.filters = {
    type: selectedType.value,
    blocked: selectedBlocked.value,
    search: searchFilter.value,
    sort_by: sortField.value,
    sort_direction: sortDirection.value
  }

  await storeUser.fetchUsers(resetPagination)
  loading.value = false
}
onMounted(async () => {
  fetchData()
})
</script>
