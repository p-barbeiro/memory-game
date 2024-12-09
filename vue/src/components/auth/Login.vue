<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useErrorStore } from '@/stores/error'
import { useAuthStore } from '@/stores/auth'
import { Button } from '@/components/ui/button'
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'

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
  <div class="w-full md:pl-0 md:py-0 h-[calc(100%-6rem)] md:rounded-xl md:shadow bg-white">
    <div class="flex flex-col md:flex-row justify-between items-center h-full">
      <div class="h-full md:w-1/2 border-r">
        <img class="md:h-full rounded md:rounded-l-xl md:rounded-r-none object-contain" src="/src/assets/img/login.jpg" alt="" />
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
                <Input id="password" type="password" placeholder="Password" v-model="credentials.password" />
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
  </div>
</template>
