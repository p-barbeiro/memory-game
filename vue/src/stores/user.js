import avatarNoneAssetURL from '@/assets/avatar-none.png'
import { toast } from '@/components/ui/toast'
import { useErrorStore } from '@/stores/error'
import axios from 'axios'
import { defineStore } from 'pinia'
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from './auth'

export const useUserStore = defineStore('user', () => {
  const storeAuth = useAuthStore()
  const storeError = useErrorStore()
  const router = useRouter()

  const users = ref([])
  const totalUsers = ref('')
  const page = ref(1)
  const filters = ref({
    type: '',
    blocked: '',
    search: '',
    sort_by: 'id',
    sort_direction: 'asc'
  })

  const resetPage = () => {
    page.value = 1
    users.value = null
  }

  const fetchUsers = async (resetPagination = false) => {
    storeError.resetMessages()
    try {
      if (resetPagination) {
        resetPage()
      }

      const queryParams = new URLSearchParams({
        page: page.value,
        ...(filters.value.type && { type: filters.value.type }),
        ...(filters.value.blocked && { blocked: filters.value.blocked }),
        ...(filters.value.search && { search: filters.value.search }),
        sort_by: filters.value.sort_by,
        sort_direction: filters.value.sort_direction
      }).toString()

      axios.defaults.headers.common.Authorization = 'Bearer ' + storeAuth.token
      axios.defaults.headers.common['Content-Type'] = 'application/json'

      const response = await axios.get(`users?${queryParams}`)

      if (response.status !== 200) {
        throw response
      }
      totalUsers.value = response.data.meta.total

      if (page.value === 1 || resetPagination) {
        users.value = response.data.data
      } else {
        users.value = [...(users.value || []), ...response.data.data]
      }

      return response.data
    } catch (e) {
      storeError.setErrorMessages(e.response.data.message, e.response.data.errors, e.response.status, 'Fetch Users Error')
      return false
    }
  }

  const fetchUsersNextPage = async () => {
    page.value++
    await fetchUsers()
  }

  const fetchUser = async (id) => {
    storeError.resetMessages()
    try {
      axios.defaults.headers.common.Authorization = 'Bearer ' + storeAuth.token
      axios.defaults.headers.common['Content-Type'] = 'application/json'

      const response = await axios.get(`users/${id}`)
      if (response.status !== 200) {
        throw response
      }

      const index = getIndexOfUser(id)
      if (index > -1) {
        users.value[index] = Object.assign({}, response.data.data)
      } else {
        users.value.push(response.data.data)
        console.log(users.value)
      }

      return response.data.data
    } catch (error) {
      storeError.setErrorMessages(e.response.data.message, e.response.data.errors, e.response.status, `Fetch User #${id} Error`)
    }
  }

  const getIndexOfUser = (id) => {
    return users.value.findIndex((p) => p.id === id)
  }

  const getUser = (id) => {
    return users.value.find((p) => p.id === id)
  }

  const getPhotoURL = (photoFileName) => {
    if (photoFileName) {
      if (import.meta.env.DEV) {
        return axios.defaults.baseURL.replaceAll('/api', photoFileName)
      }
      let occurrence = 0
      return axios.defaults.baseURL.replace(/\/api/g, (match) => {
        occurrence++
        return occurrence === 2 ? photoFileName : match
      })
    }
    return avatarNoneAssetURL
  }

  const updatePhoto = async (id, credentials) => {
    storeError.resetMessages()
    try {
      axios.defaults.headers.common['Content-Type'] = 'multipart/form-data'
      const response = await axios.post(`users/${id}/photo`, credentials)
      if (response.status !== 200) {
        throw response
      }
      const index = getIndexOfUser(id)
      if (index > -1) {
        users.value[index] = Object.assign({}, response.data.data)
      }
      toast({
        title: 'Profile Updated',
        description: 'Profile photo has been updated successfully!',
        variant: 'info'
      })
      return true
    } catch (e) {
      storeError.setErrorMessages(e.response.data.message, e.response.data.errors, e.response.status, 'Profile Photo Update Failed!')
      return false
    }
  }

  const removeAccount = async (id) => {
    storeError.resetMessages()
    try {
      axios.defaults.headers.common['Content-Type'] = 'application/json'
      axios.defaults.headers.common.Authorization = 'Bearer ' + storeAuth.token

      const response = await axios.delete(`users/${id}`)
      if (response.status !== 200) {
        throw response
      }
      toast({
        title: 'Account Deleted',
        description: 'Account has been deleted successfully!',
        variant: 'info'
      })
      router.back()
      return true
    } catch (e) {
      storeError.setErrorMessages(e.response.data.message, e.response.data.errors, e.response.status, 'Account Deletion Failed!')
      return false
    }
  }

  const updateProfile = async (id, credentials) => {
    storeError.resetMessages()
    try {
      axios.defaults.headers.common.Authorization = 'Bearer ' + storeAuth.token
      axios.defaults.headers.common['Content-Type'] = 'application/json'
      const response = await axios.patch(`users/${id}`, credentials)

      if (response.status !== 200) {
        throw response
      }

      const index = getIndexOfUser(id)
      if (index > -1) {
        users.value[index] = Object.assign({}, response.data.data)
      }

      toast({
        title: 'Profile Updated',
        description: 'Profile has been updated successfully!',
        variant: 'info'
      })
      return true
    } catch (e) {
      storeError.setErrorMessages(e.response.data.message, e.response.data.errors, e.response.status, 'Profile Update Failed!')
      return false
    }
  }

  const toggleLock = async (id) => {
    storeError.resetMessages()
    try {
      axios.defaults.headers.common.Authorization = 'Bearer ' + storeAuth.token
      axios.defaults.headers.common['Content-Type'] = 'application/json'
      const response = await axios.post(`users/${id}/toggleLock`)

      if (response.status !== 200) {
        throw response
      }

      const index = getIndexOfUser(id)
      if (index > -1) {
        users.value[index] = Object.assign({}, response.data.data)
      }

      toast({
        title: 'User Updated',
        description: `Account has been ${response.data.data.blocked ? 'blocked' : 'unblocked'} successfully!`,
        variant: 'info'
      })
      return true
    } catch (e) {
      storeError.setErrorMessages(e.response.data.message, e.response.data.errors, e.response.status, 'Profile Update Failed!')
      return false
    }
  }

  return {
    users,
    page,
    filters,
    totalUsers,
    fetchUsers,
    fetchUsersNextPage,
    resetUsersPage: resetPage,
    fetchUser,
    getUser,
    getPhotoURL,
    updatePhoto,
    removeAccount,
    updateProfile,
    toggleLock
  }
})
