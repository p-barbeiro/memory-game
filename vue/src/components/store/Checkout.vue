<template>
  <div class="flex flex-col h-full w-full place-self-center justify-center">
    <div class="flex flex-col md:flex-row w-full h-full gap-5">
      <!-- payment details -->
      <div class="md:w-1/2 flex flex-col gap-3">
        <Card>
          <CardHeader>
            <CardTitle>Confirm your details</CardTitle>
          </CardHeader>
          <CardContent>
            <form>
              <div class="grid items-center w-full gap-4">
                <div class="flex flex-col space-y-1.5">
                  <Label>Name</Label>
                  <Input v-model="auth.userName" />
                </div>

                <div class="flex flex-col space-y-1.5">
                  <Label>Email</Label>
                  <Input v-model="auth.userEmail" />
                </div>
              </div>
            </form>
          </CardContent>
        </Card>
        <Card>
          <CardHeader>
            <CardTitle>Choose your payment method</CardTitle>
          </CardHeader>
          <CardContent>
            <form>
              <div class="grid items-center w-full gap-4">
                <div class="flex flex-col space-y-1.5">
                  <Label for="payment_type">Payment Method</Label>
                  <Dropdown id="payment_type" :options="payment_types" v-model="payment_type" />
                  <ErrorMessage :errorMessage="storeError.fieldMessage('payment_type')"></ErrorMessage>
                </div>
                <div class="flex flex-col space-y-1.5">
                  <Label for="payment_reference">{{ payment_ref_txt }} <span class="text-red-600 text-sm">*</span></Label>
                  <Input :disabled="payment_type === 'MB'" id="payment_reference" v-model="payment_ref" autofocus />
                  <ErrorMessage :errorMessage="storeError.fieldMessage('payment_reference')"></ErrorMessage>
                </div>
              </div>
            </form>
          </CardContent>
        </Card>
      </div>

      <!-- summary -->
      <div class="md:w-1/2">
        <Card class="bg-gray-100 h-full flex flex-col justify-between">
          <div>
            <CardHeader>
              <CardTitle>Order summary</CardTitle>
              <CardDescription>
                Changed your mind?
                <RouterLink :to="{ name: 'store' }" class="hover:underline text-black"> Go back to store </RouterLink>
              </CardDescription>
            </CardHeader>
            <CardContent class="flex flex-col justify-center">
              <div class="flex flex-col gap-3 w-full">
                <div class="flex justify-between w-full">
                  <p>Brain Coins</p>
                  <p>
                    <b>{{ props.amount * 10 }}</b>
                  </p>
                </div>
                <hr />
                <div class="flex justify-between w-full">
                  <p>Price</p>
                  <p>
                    <b>{{ props.amount.toFixed(2) }}€</b>
                  </p>
                </div>
                <hr />
                <div class="flex justify-between w-full">
                  <p>Payment Method</p>
                  <p>
                    <b>{{ payment_type }}</b>
                  </p>
                </div>
                <hr />
                <div class="flex justify-between w-full">
                  <p>{{ payment_ref_txt }}</p>
                  <p>{{ payment_ref }}</p>
                </div>
              </div>
            </CardContent>
          </div>
          <CardFooter>
            <Button @click="handlePayment" class="w-full">
              <IconLoading v-if="loading" class="h-5 w-5 mr-2" />
              Pay {{ props.amount }}€</Button
            >
          </CardFooter>
        </Card>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card'
import { useErrorStore } from '@/stores/error'
import Button from '../ui/button/Button.vue'
import Input from '../ui/input/Input.vue'
import Label from '../ui/label/Label.vue'
import Dropdown from '../ui/dropdown/Dropdown.vue'
import { computed, ref } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { inject } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'
import IconLoading from '../icons/IconLoading.vue'

const auth = useAuthStore()
const storeError = useErrorStore()
const alertDialog = inject('alertDialog')
const router = useRouter()
const loading = ref(false)

const props = defineProps({
  amount: {
    type: Number,
    required: true
  }
})

const payment_types = [
  { value: 'MBWAY', label: 'MBWAY' },
  { value: 'PAYPAL', label: 'PAYPAL' },
  { value: 'IBAN', label: 'IBAN' },
  { value: 'MB', label: 'MB' },
  { value: 'VISA', label: 'VISA' }
]
const payment_type = ref(payment_types[0].value)

const payment_ref = ref('')
const payment_ref_txt = computed(() => {
  switch (payment_type.value) {
    case 'MBWAY':
      payment_ref.value = ''
      return 'Phone Number'
    case 'PAYPAL':
      payment_ref.value = auth.userEmail
      return 'Email'
    case 'IBAN':
      payment_ref.value = ''
      return 'IBAN'
    case 'MB':
      payment_ref.value = Math.floor(10000 + Math.random() * 90000) + '-' + Math.floor(100000000 + Math.random() * 900000000)
      return 'MB Reference'
    case 'VISA':
      payment_ref.value = ''
      return 'Card Number'
  }
})

const handlePayment = async () => {
  if (!payment_ref.value) {
    document.getElementById('payment_reference').focus()
    return
  }
  loading.value = true
  storeError.resetMessages()
  try {
    axios.defaults.headers.common.Authorization = 'Bearer ' + auth.token
    axios.defaults.headers.common['Content-Type'] = 'application/json'

    const body = {
      type: 'P',
      euros: props.amount,
      payment_type: payment_type.value,
      payment_reference: payment_ref.value,
      brain_coins: props.amount * 10
    }

    const response = await axios.post(`transactions`, body)

    if (response.status === 201) {
      const transaction = response.data.data
      if (await paymentGateway(payment_type.value, payment_ref.value, props.amount, transaction.id)) {
        loading.value = false
        auth.user.brain_coins += transaction.brain_coins
        alertDialog.value.open(onSuccess, 'Payment Processing Successful', '', `Finish`, `Thank you for your payment. <br>You have successfully purchased ${props.amount * 10} Brain Coins.`)
      }
      return transaction
    }
  } catch (e) {
    storeError.setErrorMessages(e.response.data.message, e.response.data.errors, e.response.status, 'Error creating transaction')
    return false
  }
}

const onSuccess = () => {
  router.push({ name: 'home' })
}

const paymentGateway = async (type, reference, value, transactionID) => {
  storeError.resetMessages()
  try {
    axios.defaults.headers.common['Content-Type'] = 'application/json'

    const body = {
      type: type,
      value: value,
      reference: reference
    }

    const response = await axios.post(`https://dad-202425-payments-api.vercel.app/api/debit`, body)
    console.log(response)

    if (response.status === 201) {
      return true
    }
  } catch (e) {
    storeError.setErrorMessages(e.response.data.message, undefined, undefined, 'Payment Gateway Error')
    deleteTransaction(transactionID)
    return false
  }
}

const deleteTransaction = async (transactionID) => {
  try {
    axios.defaults.headers.common.Authorization = 'Bearer ' + auth.token
    axios.defaults.headers.common['Content-Type'] = 'application/json'

    const response = await axios.delete(`transactions/${transactionID}`)
    console.log(response)
  } catch (e) {
    storeError.setErrorMessages(e.response.data.message, undefined, undefined, 'Error deleting transaction')
  }
}
</script>
