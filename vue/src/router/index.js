import Login from '@/components/auth/Login.vue'
import HomeComponent from '@/components/HomeComponent.vue'
import LaravelTester from '@/components/LaravelTester.vue'
import WebSocketTester from '@/components/WebSocketTester.vue'
import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { isLoading } from '@/lib/utils'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'home',
      component: HomeComponent,
      meta: {
        title: 'Home'
      }
    },
    {
      path: '/login',
      name: 'login',
      component: Login,
      meta: {
        title: 'Login'
      }
    },
    {
      path: '/testers',
      children: [
        {
          path: 'laravel',
          component: LaravelTester,
          name: 'laravelTester',
          meta: {
            title: 'Laravel Tester'
          }
        },
        {
          path: 'websocket',
          component: WebSocketTester,
          name: 'webSocketTester',
          meta: {
            title: 'WebSocket Tester'
          }
        }
      ]
    }
  ]
})


let handlingFirstRoute = true

router.beforeEach(async (to, from, next) => {
    const storeAuth = useAuthStore()
    isLoading.value = true
    if (handlingFirstRoute) {
        handlingFirstRoute = false
        await storeAuth.restoreToken()
    }

    //if user is auth, login page is not accessible
    if ((to.name == 'login') && (storeAuth.user)) {
        next({ name: 'home' })
        return
    }
    // // routes "updateTask" and "updateProject" are only accessible when user is logged in
    // if (((to.name == 'updateTask') || (to.name == 'updateProject')) && (!storeAuth.user)) {
    //     next({ name: 'login' })
    //     return
    // }
    // all other routes are accessible to everyone, including anonymous users
    next()
    isLoading.value = false
})
    
export default router
