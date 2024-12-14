<script setup>
import { onMounted, ref } from 'vue'
import { useRouter } from 'vue-router'
import { useErrorStore } from '@/stores/error'
import { useAuthStore } from '@/stores/auth'
import { Button } from '@/components/ui/button'
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import IconLoading from '../icons/IconLoading.vue'

const router = useRouter()
const storeAuth = useAuthStore()
const storeError = useErrorStore()

const credentials = ref({
  email: '',
  password: ''
})

const cancel = () => {
  router.back()
}

const login = async () => {
  const user = await storeAuth.login(credentials.value)
  if (user) {
    router.back()
  }
}

</script>

<template>

  <div class="place-self-center flex flex-col md:flex-row justify-between items-center h-full w-full">
    <div class="h-full md:w-1/2 md:border-r">
      <img class="md:h-full w-full rounded md:rounded-l-xl md:rounded-r-none object-contain" src="/src/assets/img/login.jpg" alt="" loading="eager"/>
    </div>
    <Card class="md:w-1/2 w-full border-none shadow-none">
      <CardHeader>
        <CardTitle>Login</CardTitle>
        <CardDescription>
          Dont have account?
          <RouterLink :to="{ name: 'register' }" class="hover:underline text-black"> Sign up here </RouterLink>
        </CardDescription>
      </CardHeader>
      <CardContent>
        <form>
          <div class="grid items-center w-full gap-4">
            <div class="flex flex-col space-y-1.5">
              <Label for="email">Email</Label>
              <Input id="email" type="email" placeholder="User Email" v-model="credentials.email" />
              <ErrorMessage :errorMessage="storeError.fieldMessage('email')"></ErrorMessage>
            </div>
            <div class="flex flex-col space-y-1.5">
              <Label for="password">Password</Label>
              <Input @keydown.enter="login" id="password" type="password" placeholder="Password" v-model="credentials.password" />
              <ErrorMessage :errorMessage="storeError.fieldMessage('password')"></ErrorMessage>
            </div>
          </div>
        </form>
      </CardContent>
      <CardFooter class="flex justify-between px-6 pb-6">
        <Button variant="outline" @click="cancel"> Cancel </Button>
        <Button @click="login"> Login </Button>
      </CardFooter>
    </Card>
  </div>
</template>
