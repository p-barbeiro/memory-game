<template>
  <div class="w-full p-8 h-[calc(100%-6rem)] md:rounded-xl md:shadow bg-white">
    <div class="flex flex-col justify-between h-full w-full">
      <CardFooter class="flex justify-between">
        <CardTitle>Profile Information</CardTitle>
        <div class="flex flex-row gap-3">
          <Button variant="destructive" @click="removeAcount"> Remove </Button>
          <Button @click="toogleEdit"> {{ editText }} </Button>
        </div>
      </CardFooter>
      <div class="w-full h-full flex flex-col lg:flex-row">
        <div class="p-6 lg:w-1/3 h-full flex flex-col items-center justify-center">
          <div v-if="!viewMode" class="flex flex-col justify-between space-y-1.5 mb-5 w-full">
            <Label for="photo_filename">Update photo</Label>
            <div class="flex flex-row items-center gap-2">
              <div class="relative flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50">
                <IconPhoto />
                <input type="file" id="photo_filename" name="photo_filename" accept="image/*" @change="onFileChange" class="hidden w-full h-full cursor-pointer" />
                <label for="photo_filename" class="flex items-center justify-center w-full h-full cursor-pointer">
                  <span v-if="credentials.photo_filename" class="truncate">{{ credentials.photo_filename.name }}</span>
                  <span v-else>Choose a file...</span>
                </label>
              </div>
              <ErrorMessage :errorMessage="storeError.fieldMessage('photo_filename')"></ErrorMessage>
              <Button v-if="credentials.photo_filename" @click="updatePhoto" class="w-1/3">Save</Button>
            </div>
          </div>
          <img class="w-full sm:w-1/3 lg:w-full object-cover rounded-xl border shadow" :src="auth.userPhotoUrl" alt="Profile Picture" />
        </div>
        <div class="lg:w-2/3 h-full flex flex-col justify-center">
          <CardContent>
            <form>
              <div class="grid items-center w-full gap-4">
                <div class="flex flex-col space-y-1.5">
                  <Label for="email">Email</Label>
                  <Input :disabled="viewMode" id="email" type="email" placeholder="User Email" v-model="credentials.email" />
                  <ErrorMessage :errorMessage="storeError.fieldMessage('email')"></ErrorMessage>
                </div>

                <div class="flex flex-col space-y-1.5">
                  <Label for="password">Password</Label>
                  <Input :disabled="viewMode" id="password" type="password" v-model="credentials.password" />
                  <ErrorMessage :errorMessage="storeError.fieldMessage('password')"></ErrorMessage>
                </div>

                <div class="flex flex-col space-y-1.5">
                  <Label for="name">Name</Label>
                  <Input :disabled="viewMode" id="name" type="text" v-model="credentials.name" />
                  <ErrorMessage :errorMessage="storeError.fieldMessage('name')"></ErrorMessage>
                </div>

                <div class="flex flex-col space-y-1.5">
                  <Label for="nickname">Nickname</Label>
                  <Input :disabled="viewMode" id="nickname" type="text" v-model="credentials.nickname" />
                  <ErrorMessage :errorMessage="storeError.fieldMessage('nickname')"></ErrorMessage>
                </div>
              </div>
            </form>
            <Button v-if="!viewMode" @click="updateProfile" class="w-full mt-5">Update Profile</Button>
          </CardContent>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Button, buttonVariants } from '@/components/ui/button'
import { CardContent, CardFooter, CardTitle } from '@/components/ui/card'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { useAuthStore } from '@/stores/auth'
import { useErrorStore } from '@/stores/error'
import { ref, inject, computed } from 'vue'
import { useRouter } from 'vue-router'
import IconPhoto from '../icons/IconPhoto.vue'
import { toast } from '@/components/ui/toast'

const inputDialog = inject('inputDialog')

const router = useRouter()
const storeAuth = useAuthStore()
const storeError = useErrorStore()

const auth = useAuthStore()

const onFileChange = (e) => {
  const file = e.target.files[0]
  credentials.value.photo_filename = file
}

const editText = ref('Edit')
const viewMode = ref(true)
const toogleEdit = () => {
  storeError.resetMessages()
  viewMode.value = !viewMode.value
  editText.value = viewMode.value ? 'Edit' : 'Close'
  if (viewMode.value) {
    restoreValues()
  }
}

const restoreValues = () => {
  credentials.value = {
    name: auth.user?.name || '',
    email: auth.user?.email || '',
    nickname: auth.user?.nickname || '',
    photo_filename: ''
  }
}

const credentials = ref({
  name: auth.user?.name || '',
  email: auth.user?.email || '',
  password: '',
  nickname: auth.user?.nickname || '',
  photo_filename: ''
})

const updateProfile = async () => {
  const validated = Object.assign({}, credentials.value)
  delete validated.photo_filename
  if (!credentials.value.password) delete validated.password
  if (credentials.value.email === auth.user.email) delete validated.email
  if (credentials.value.name === auth.user.name) delete validated.name
  if (credentials.value.nickname === auth.user.nickname) delete validated.nickname

  if (Object.keys(validated).length === 0) {
    toogleEdit()
    return
  }

  const user = await storeAuth.updateProfile(validated)
  if (user) {
    toogleEdit()
  }
}

const updatePhoto = async () => {
  const photo = {
    photo_filename: credentials.value.photo_filename
  }
  storeAuth.updatePhoto(photo)
  delete credentials.value.photo_filename
}

const removeAcount = () => {
  inputDialog.value.open(removeAccountConfirmation, 'Remove Account', 'Are you sure you want to remove your account? Your Brain coins will not be refunded', `Type "remove-${auth.userNickname}" to confirm`, '', 'Cancel', 'Remove')
}

const removeAccountConfirmation = (message) => {
  if (message === `remove-${auth.userNickname}`) {
    // storeAuth.removeAccount()
    // router.push({ name: 'home' })
    console.log('DEBUG: Remove account')
  } else {
    toast({
      title: 'Account Removal Failed',
      description: 'Incorrect confirmation message',
      variant: 'destructive'
    })
  }
}
</script>
