import { useToast } from '@/components/ui/toast/use-toast'
import { useErrorStore } from '@/stores/error'
import axios from 'axios'
import { defineStore } from 'pinia'
import { computed, ref } from 'vue'

export const useBoardStore = defineStore('board', () => {
  const storeError = useErrorStore()
  const { toast } = useToast()

  const boards = ref([])
  const totalBoards = computed(() => boards.value.length)

  const fetchBoards = async () => {
    storeError.resetMessages()
    try {
      const response = await axios.get('boards')
      boards.value = response.data.data
    } catch (e) {
      storeError.setErrorMessages(e.response.data.message, e.response.data.errors, e.response.status, 'Error fetching Boards!')
      return false
    }
  }

  const getBoardIndex = (id) => {
    return boards.value.findIndex((p) => p.id === id)
  }

  const getBoard = (id) => {
    return boards.value.find((p) => p.id === id)
  }

  const fetchBoard = async (boardID) => {
    storeError.resetMessages()
    const response = await axios.get('boards/' + boardID)
    const index = getBoardIndex(boardID)
    if (index > -1) {
      boards.value[index] = Object.assign({}, response.data.data)
    }
    return response.data.data
  }

  return {
    boards,
    totalBoards,
    fetchBoard,
    fetchBoards,
    getBoard
  }
})
