import Login from '@/components/auth/Login.vue'
import Register from '@/components/auth/Register.vue'
import RegisterSucess from '@/components/auth/RegisterSucess.vue'
import HomeComponent from '@/components/HomeComponent.vue'
import LaravelTester from '@/components/LaravelTester.vue'
import BoardSelector from '@/components/board/BoardSelector.vue'
import GameMode from '@/components/gamemodeselector/GameMode.vue'
import Game from '@/components/game/Game.vue'
import WebSocketTester from '@/components/WebSocketTester.vue'
import { isLoading } from '@/lib/utils'
import { useAuthStore } from '@/stores/auth'
import { createRouter, createWebHistory } from 'vue-router'
import Profile from '@/components/profile/Profile.vue'
import GameHistory from '@/components/gamehistory/GameHistory.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    //home
    {
      path: '/',
      name: 'home',
      component: HomeComponent,
      meta: {
        title: 'Home'
      }
    },
    //login
    {
      path: '/login',
      name: 'login',
      component: Login,
      meta: {
        title: 'Login'
      }
    },
    //register
    {
      path: '/register',
      name: 'register',
      component: Register,
      meta: {
        title: 'Register'
      }
    },
    //register success
    {
      path: '/register-success',
      name: 'registerSuccess',
      component: RegisterSucess,
      meta: {
        title: 'Account successfully created!'
      }
    },
    //profile
    {
      path: '/profile',
      name: 'profile',
      component: Profile,
      meta: {
        title: 'My Profile'
      }
    },
    //history
    //profile
    {
      path: '/game-history',
      name: 'history',
      component: GameHistory,
      meta: {
        title: 'My Game History'
      }
    },
    //gamemode
    {
      path: '/gamemode',
      name: 'gamemode',
      component: GameMode,
      meta: {
        title: 'Choose your game mode!'
      }
    },
    //board
    {
      path: '/board-selector',
      name: 'boardSelector',
      component: BoardSelector,
      meta: {
        title: 'Choose your board!'
      }
    },
    //game
    {
      path: '/game/:id',
      name: 'game',
      component: Game,
      props: (route) => ({ id: parseInt(route.params.id) }),
      meta: {
        title: 'Flip, match, and win!'
      }
    },
    //testers
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

  const authPagesDisable = ['login', 'register', 'registerSuccess']
  if (authPagesDisable.includes(to.name) && storeAuth.user) {
    next({ name: 'home' })
    return
  }

  const guestPagesDisable = ['profile']
  if (guestPagesDisable.includes(to.name) && !storeAuth.user) {
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
