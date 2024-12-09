<script setup>
import { Button } from '@/components/ui/button'
import { Card, CardContent, CardFooter, CardHeader, CardTitle } from '@/components/ui/card'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { useAuthStore } from '@/stores/auth'
import { useErrorStore } from '@/stores/error'
import { ref,computed } from 'vue'
import { useRouter } from 'vue-router'
import IconPhoto from '../icons/IconPhoto.vue'

const router = useRouter()
const storeAuth = useAuthStore()
const storeError = useErrorStore()

const credentials = ref({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
  nickname: '',
  photo_filename: ''
})

const cancel = () => {
  router.back()
}

const register = async () => {
  try {
    const success = await storeAuth.register(credentials.value)
    if (success) {
      await storeAuth.login({
        email: credentials.value.email,
        password: credentials.value.password
      })
      router.push({ name: 'register-success' })
    }
  } catch (error) {
    console.error('Registration failed:', error)
  }
}

const onFileChange = (e) => {
  const file = e.target.files[0]
  credentials.value.photo_filename = file
}
</script>

<template>
  <div class="w-full md:pl-0 md:py-0 h-[calc(100%-6rem)] md:rounded-xl md:shadow bg-white">
    <div class="flex flex-col md:flex-row justify-between items-center h-full">
      <div class="h-full md:w-1/2 border-r">
        <img class="md:h-full rounded md:rounded-l-xl md:rounded-r-none object-contain" src="/src/assets/img/login.jpg" alt="" />
      </div>
      <Card class="md:w-1/2 w-full border-none shadow-none">
        <CardHeader>
          <CardTitle>Register</CardTitle>
        </CardHeader>
        <CardContent>
          <form>
            <div class="grid items-center w-full gap-4">
              <div class="flex flex-col space-y-1.5">
                <Label for="email">Email <span class="text-red-600 text-sm">*</span></Label>
                <Input id="email" type="email" placeholder="User Email" v-model="credentials.email" />
                <ErrorMessage :errorMessage="storeError.fieldMessage('email')"></ErrorMessage>
              </div>
              <div class="flex flex-col space-y-1.5">
                <Label for="password">Password <span class="text-red-600 text-sm">*</span></Label>
                <Input id="password" type="password" v-model="credentials.password" />
                <ErrorMessage :errorMessage="storeError.fieldMessage('password')"></ErrorMessage>
              </div>
              <div class="flex flex-col space-y-1.5">
                <Label for="password_confirmation">Confirm Password <span class="text-red-600 text-sm">*</span></Label>
                <Input id="password_confirmation" type="password" v-model="credentials.password_confirmation" />
                <ErrorMessage :errorMessage="storeError.fieldMessage('password_confirmation')"></ErrorMessage>
              </div>
              <div class="flex flex-col space-y-1.5">
                <Label for="name">Name <span class="text-red-600 text-sm">*</span></Label>
                <Input id="name" type="text" v-model="credentials.name" />
                <ErrorMessage :errorMessage="storeError.fieldMessage('name')"></ErrorMessage>
              </div>
              <div class="flex flex-col space-y-1.5">
                <Label for="nickname">Nickname <span class="text-red-600 text-sm">*</span></Label>
                <Input id="nickname" type="text" v-model="credentials.nickname" />
                <ErrorMessage :errorMessage="storeError.fieldMessage('nickname')"></ErrorMessage>
              </div>

              <div class="flex flex-col space-y-1.5">
                <Label for="photo_filename">Photo</Label>
                <div class="relative flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50">
                  <IconPhoto />
                  <input type="file" id="photo_filename" name="photo_filename" accept="image/*" @change="onFileChange" class="hidden w-full h-full cursor-pointer" />
                  <label for="photo_filename" class="flex items-center justify-center w-full h-full cursor-pointer">
                    <span v-if="credentials.photo_filename">{{ credentials.photo_filename.name }}</span>
                    <span v-else>Choose a file...</span>
                  </label>
                </div>
                <ErrorMessage :errorMessage="storeError.fieldMessage('photo_filename')"></ErrorMessage>
              </div>
            </div>
          </form>
        </CardContent>
        <CardFooter class="flex justify-between px-6 pb-6">
          <Button variant="outline" @click="cancel"> Cancel </Button>
          <Button @click="register"> Submit </Button>
        </CardFooter>
      </Card>
    </div>
  </div>
</template>
