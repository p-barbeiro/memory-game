<template>
  <div class="flex flex-col h-full w-full justify-center">
    <div class="space-y-4 mt-5" v-if="storeBoard.boards">
      <div class="rounded-md border">
        <Table>
          <TableHeader>
            <TableRow class="text-nowrap">
              <TableHead>Board ID</TableHead>
              <TableHead>Columns</TableHead>
              <TableHead>Rows</TableHead>
              <TableHead>Description</TableHead>
              <TableHead>Price</TableHead>
              <TableHead>Guest available</TableHead>
            </TableRow>
          </TableHeader>
          <TableBody>
            <TableRow v-for="board in storeBoard.boards" :key="board.id" >
              <TableCell>{{ board.id }}</TableCell>
              <TableCell>{{ board.columns }}</TableCell>
              <TableCell>{{ board.rows }}</TableCell>
              <TableCell>{{ board.description }}</TableCell>
              <TableCell class="text-nowrap">{{ board.price > 0 ? board.price + ' Brain Coin' : 'Free' }} </TableCell>
              <TableCell>
                <Badge :variant="board.guest_enable === true ? 'green' : 'red'">
                  {{ board.guest_enable === true ? 'Yes' : 'No' }}
                </Badge>
              </TableCell>
            </TableRow>
            <TableRow v-if="!storeBoard.boards?.length">
              <TableCell colspan="9" class="text-center h-24">No boards found</TableCell>
            </TableRow>
          </TableBody>
        </Table>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table'
import { onMounted } from 'vue'
import { useBoardStore } from '@/stores/board'
import Badge from '../ui/badge/Badge.vue';

const storeBoard = useBoardStore()

const fetchData = async (resetPagination = false) => {
  await storeBoard.fetchBoards(resetPagination)
}
onMounted(async () => {
  fetchData()
})
</script>
