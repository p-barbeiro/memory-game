import { useErrorStore } from '@/stores/error'
import axios from 'axios'
import { defineStore } from 'pinia'
import { computed, inject, ref } from 'vue'
import { useRouter } from 'vue-router'

import avatarNoneAssetURL from '@/assets/avatar-none.png'
import { toast } from '@/components/ui/toast'

export const useAuthStore = defineStore('auth', () => {
  const router = useRouter()
  const storeError = useErrorStore()
  const socket = inject('socket')

  const user = ref(null)
  const token = ref('')
  let intervalToRefreshToken = null

  const userName = computed(() => {
    return user.value ? user.value.name : ''
  })

  const userFirstLastName = computed(() => {
    const names = userName.value.trim().split(' ')
    const firstName = names[0] ?? ''
    const lastName = names.length > 1 ? names[names.length - 1] : ''
    return (firstName + ' ' + lastName).trim()
  })

  const userEmail = computed(() => {
    return user.value ? user.value.email : ''
  })

  const userType = computed(() => {
    return user.value ? user.value.type : ''
  })

  const userGender = computed(() => {
    return user.value ? user.value.gender : ''
  })

  const userBrainCoins = computed(() => {
    return user.value ? user.value.brain_coins : ''
  })

  const userPhotoUrl = computed(() => {
    const photoFile = user.value ? (user.value.photoFileName ?? '') : ''
    if (photoFile) {
      if (import.meta.env.DEV) {
        return axios.defaults.baseURL.replaceAll('/api', photoFile)
      }
      let occurrence = 0
      return axios.defaults.baseURL.replace(/\/api/g, (match) => {
        occurrence++
        return occurrence === 2 ? photoFile : match
      })
    }
    return avatarNoneAssetURL
  })

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

  const isPlayer = computed(() => {
    return user.value ? user.value.type === 'Player' : false
  })

  const isAdmin = computed(() => {
    return user.value ? user.value.type === 'Administrator' : false
  })

  const userID = computed(() => {
    return user.value ? user.value.id : ''
  })

  const userNickname = computed(() => {
    return user.value ? user.value.nickname : ''
  })

  // This function is "private" - not exported by the store
  const clearUser = () => {
    resetIntervalToRefreshToken()
    if (user.value) {
      socket.emit('logout', user.value)
    }
    user.value = null
    token.value = ''
    localStorage.removeItem('token')
    axios.defaults.headers.common.Authorization = ''
  }

  const login = async (credentials) => {
    storeError.resetMessages()
    try {
      const responseLogin = await axios.post('auth/login', credentials)
      token.value = responseLogin.data.token
      localStorage.setItem('token', token.value)
      axios.defaults.headers.common.Authorization = 'Bearer ' + token.value
      const responseUser = await axios.get('users/me')
      user.value = responseUser.data.data
      socket.emit('login', user.value)
      repeatRefreshToken()
      toast({
        title: 'Login Successful',
        variant: 'info'
      })
      return user.value
    } catch (e) {
      clearUser()
      storeError.setErrorMessages(e.response.data.message, e.response.data.errors, e.response.status, 'Authentication Error!')
      return false
    }
  }

  const logout = async () => {
    storeError.resetMessages()
    try {
      await axios.post('auth/logout')
      clearUser()
      return true
    } catch (e) {
      clearUser()
      storeError.setErrorMessages(e.response.data.message, [], e.response.status, 'Authentication Error!')
      return false
    }
  }

  const resetIntervalToRefreshToken = () => {
    if (intervalToRefreshToken) {
      clearInterval(intervalToRefreshToken)
    }
    intervalToRefreshToken = null
  }

  const repeatRefreshToken = () => {
    if (intervalToRefreshToken) {
      clearInterval(intervalToRefreshToken)
    }
    intervalToRefreshToken = setInterval(
      async () => {
        try {
          const response = await axios.post('auth/refreshtoken')
          token.value = response.data.token
          localStorage.setItem('token', token.value)
          axios.defaults.headers.common.Authorization = 'Bearer ' + token.value
          return true
        } catch (e) {
          clearUser()
          storeError.setErrorMessages(e.response.data.message, e.response.data.errors, e.response.status, 'Authentication Error!')
          return false
        }
      },
      1000 * 60 * 110
    ) // repeat every 110 minutes
    return intervalToRefreshToken
  }

  const restoreToken = async function () {
    let storedToken = localStorage.getItem('token')
    if (storedToken) {
      try {
        token.value = storedToken
        axios.defaults.headers.common.Authorization = 'Bearer ' + token.value
        const responseUser = await axios.get('users/me')
        user.value = responseUser.data.data
        socket.emit('login', user.value)
        repeatRefreshToken()
        return true
      } catch {
        clearUser()
        return false
      }
    }
    return false
  }

  const register = async (credentials, type = undefined) => {
    storeError.resetMessages()
    try {
      if (type) {
        credentials.type = type
      }

      axios.defaults.headers.common['Content-Type'] = 'multipart/form-data'
      const response = await axios.post('auth/register', credentials)
      if (response.status !== 201) {
        throw response
      }
      if (!type) router.push({ name: 'registerSuccess' })
      return true
    } catch (e) {
      storeError.setErrorMessages(e.response.data.message, e.response.data.errors, e.response.status, 'Registration Failed!')
      return false
    }
  }

  const updateProfile = async (credentials) => {
    storeError.resetMessages()
    try {
      axios.defaults.headers.common.Authorization = 'Bearer ' + token.value
      axios.defaults.headers.common['Content-Type'] = 'application/json'
      const response = await axios.patch(`users/${user.value.id}`, credentials)

      if (response.status !== 200) {
        throw response
      }
      user.value = response.data.data
      toast({
        title: 'Profile Updated',
        description: 'Your profile has been updated successfully!',
        variant: 'info'
      })
      return true
    } catch (e) {
      storeError.setErrorMessages(e.response.data.message, e.response.data.errors, e.response.status, 'Profile Update Failed!')
      return false
    }
  }

  const updatePhoto = async (credentials) => {
    storeError.resetMessages()
    try {
      axios.defaults.headers.common.Authorization = 'Bearer ' + token.value
      axios.defaults.headers.common['Content-Type'] = 'multipart/form-data'
      const response = await axios.post(`users/${user.value.id}/photo`, credentials)
      if (response.status !== 200) {
        throw response
      }
      user.value = response.data.data
      toast({
        title: 'Profile Updated',
        description: 'Your profile photo has been updated successfully!',
        variant: 'info'
      })
      return true
    } catch (e) {
      storeError.setErrorMessages(e.response.data.message, e.response.data.errors, e.response.status, 'Profile Photo Update Failed!')
      return false
    }
  }

  const removeAccount = async () => {
    storeError.resetMessages()
    try {
      axios.defaults.headers.common['Content-Type'] = 'application/json'
      axios.defaults.headers.common.Authorization = 'Bearer ' + token.value

      const response = await axios.delete(`users/${user.value.id}`)
      if (response.status !== 200) {
        throw response
      }
      clearUser()
      router.push({ name: 'home' })
      toast({
        title: 'Account Deleted',
        description: 'Your account has been deleted successfully!',
        variant: 'info'
      })
      return true
    } catch (e) {
      storeError.setErrorMessages(e.response.data.message, e.response.data.errors, e.response.status, 'Account Deletion Failed!')
      return false
    }
  }

  return {
    user,
    userName,
    userFirstLastName,
    userEmail,
    userType,
    userGender,
    userPhotoUrl,
    userBrainCoins,
    isPlayer,
    token,
    userNickname,
    userID,
    isAdmin,
    login,
    logout,
    restoreToken,
    register,
    updateProfile,
    updatePhoto,
    getPhotoURL,
    removeAccount
  }
})
