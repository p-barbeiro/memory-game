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
import Transactions from '@/components/transactions/Transactions.vue'
import Scoreboard from '@/components/scoreboards/Scoreboard.vue'
import Store from '@/components/store/Store.vue'
import Checkout from '@/components/store/Checkout.vue'
import Lobby from '@/components/multiplayer/Lobby.vue'

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
    //registerSuccess
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
    {
      path: '/game-history',
      name: 'history',
      component: GameHistory,
      meta: {
        title: 'My Game History'
      }
    },
    //transactions
    {
      path: '/transactions',
      name: 'transactions',
      component: Transactions,
      meta: {
        title: 'My Transactions'
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
    //boardSelector
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
      path: '/game/:gameid?',
      name: 'game',
      component: Game,
      props: (route) => ({ gameid: parseInt(route.params.gameid) }),
      meta: {
        title: 'Flip, match, and win!'
      }
    },
    //store
    {
      path: '/store',
      name: 'store',
      component: Store,
      meta: {
        title: 'Fuel Your Fun – Buy Coins Now!'
      }
    },
        //lobby
        {
          path: '/lobby',
          name: 'lobby',
          component: Lobby,
          meta: {
            title: 'Prepare yourself for battle!'
          }
        },
    //checkout
    {
      path: '/store/checkout/:amount',
      name: 'checkout',
      component: Checkout,
      props: (route) => ({ amount: parseInt(route.params.amount) }),
      meta: {
        title: 'Checkout'
      }
    },
    //scoreboard
    {
      path: '/scoreboard',
      name: 'scoreboard',
      component: Scoreboard,
      meta: {
        title: 'Scoreboard'
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
 
  //authenticated users can't acess this pages
  const authPagesDisable = ['login', 'register', 'registerSuccess']
  if (authPagesDisable.includes(to.name) && storeAuth.user) {
    next({ name: 'home' })
    return
  }

  //guest users can't acess this pages
  const guestPagesDisable = ['profile', 'history', 'history', 'transactions', 'store']
  if (guestPagesDisable.includes(to.name) && !storeAuth.user) {
    next({ name: 'home' })
    return
  }

  next()
  isLoading.value = false
})

export default router
