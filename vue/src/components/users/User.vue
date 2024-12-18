<template>
  <div class="flex flex-col justify-between h-full w-full">
    <CardFooter class="flex justify-between px-0">
      <CardTitle>Profile Information</CardTitle>
      <div class="flex flex-row gap-3">
        <Button v-if="storeAuth.isAdmin" variant="destructive" @click="removeAcount"> Remove </Button>
        <Button v-if="storeAuth.isAdmin" variant="destructive" @click="toggleLock">{{ user?.blocked ? 'Unblock' : 'Block' }} </Button>
        <Button @click="toogleEdit"> {{ editText }} </Button>
      </div>
    </CardFooter>
    <div class="w-full h-full flex flex-col lg:flex-row">
      <div class="lg:w-2/3 h-full flex flex-col justify-center">
        <CardContent class="px-0">
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
      <div class="lg:w-1/3 h-full flex flex-col items-center justify-center md:p-5">
        <img class="w-full sm:w-1/3 lg:w-full object-cover rounded-xl border shadow" :src="photoUrl" alt="Profile Picture" />
        <div v-if="!viewMode" class="flex flex-col justify-between space-y-1.5 mt-5 w-full">
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
      </div>
    </div>
  </div>
</template>

<script setup>
import { Button } from '@/components/ui/button'
import { CardContent, CardFooter, CardTitle } from '@/components/ui/card'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { toast } from '@/components/ui/toast'
import { useAuthStore } from '@/stores/auth'
import { useErrorStore } from '@/stores/error'
import { useUserStore } from '@/stores/user'
import { computed, inject, onMounted, ref, watch } from 'vue'
import IconPhoto from '../icons/IconPhoto.vue'

const inputDialog = inject('inputDialog')
const storeAuth = useAuthStore()
const storeError = useErrorStore()
const storeUser = useUserStore()

const props = defineProps({
  id: { type: Number, required: true }
})

onMounted(() => {
  storeUser.fetchUser(props.id)
})

const user = computed(() => {
  return storeUser.getUser(props.id)}
)
watch(user, (newValue) => {
  if (newValue) {
    restoreValues()
  }
})

const photoUrl = computed(() => storeUser.getPhotoURL(user.value?.photoFileName))

const credentials = ref({
  name: user.value?.name || '',
  email: user.value?.email || '',
  password: '',
  nickname: user.value?.nickname || '',
  photo_filename: ''
})

const restoreValues = () => {
  credentials.value = {
    name: user.value?.name || '',
    email: user.value?.email || '',
    nickname: user.value?.nickname || '',
    photo_filename: ''
  }
}

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

const updateProfile = async () => {
  const changedFields = Object.assign({}, credentials.value)
  delete changedFields.photo_filename
  if (!credentials.value.password) delete changedFields.password
  if (credentials.value.email === user.value?.email) delete changedFields.email
  if (credentials.value.name === user.value?.name) delete changedFields.name
  if (credentials.value.nickname === user.value?.nickname) delete changedFields.nickname

  if (Object.keys(changedFields).length === 0) {
    toogleEdit()
    return
  }

  const updatedUser = await storeUser.updateProfile(user.value?.id, changedFields)
  console.log(updatedUser)

  if (updatedUser) {
    toogleEdit()
  }
}

const updatePhoto = async () => {
  const photo = {
    photo_filename: credentials.value.photo_filename
  }
  storeUser.updatePhoto(user.value?.id, photo)
  delete credentials.value.photo_filename
}

const removeAcount = () => {
  inputDialog.value.open(removeAccountConfirmation, 'Remove Account', 'Are you sure you want to remove your account? Your Brain coins will not be refunded', `Type "remove-${user.value?.nickname}" to confirm`, '', 'Cancel', 'Remove')
}

const removeAccountConfirmation = (message) => {
  if (message === `remove-${user.value?.nickname}`) {
    storeUser.removeAccount(user.value?.id)
  } else {
    toast({
      title: 'Account Removal Failed',
      description: 'Incorrect confirmation message',
      variant: 'destructive'
    })
  }
}

const toggleLock = () => {
  storeUser.toggleLock(user.value?.id)
}

</script>
