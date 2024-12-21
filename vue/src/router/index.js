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
import Users from '@/components/users/Users.vue'
import User from '@/components/users/User.vue'
import RegisterAdmin from '@/components/auth/RegisterAdmin.vue'
import Boards from '@/components/board/Boards.vue'
import Games from '@/components/multiplayer/Games.vue'
import Statistics from '@/components/statistics/Statistics.vue'
import PersonalStatistics from '@/components/statistics/PersonalStatistics.vue'
import AdminStatistics from '@/components/statistics/AdminStatistics.vue'

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
    //administration
    {
      path: '/users',
      component: Users,
      name: 'allUsers',
      meta: {
        title: 'All Users'
      }
    },
    {
      path: '/users/:id',
      component: User,
      name: 'user',
      props: (route) => ({ id: parseInt(route.params.id) }),
      meta: {
        title: 'User Profile'
      }
    },
    {
      path: '/users/addAdmin',
      component: RegisterAdmin,
      name: 'registerAdmin',
      meta: {
        title: 'Administrator Registration'
      }
    },
    {
      path: '/transactions',
      component: Transactions,
      name: 'allTransactions',
      meta: {
        title: 'All Transactions'
      }
    },
    {
      path: '/games',
      component: GameHistory,
      name: 'allGames',
      meta: {
        title: 'All Games'
      }
    },
    {
      path: '/boards',
      component: Boards,
      name: 'allBoards',
      meta: {
        title: 'All Boards'
      }
    },
    {
      path: '/statistics/personal',
      component: PersonalStatistics,
      name: 'personalStatistics',
      meta: {
        title: 'Summary and Statistics'
      }
    },
    {
      path: '/statistics/global',
      component: Statistics,
      name: 'globalStatistics',
      meta: {
        title: 'Summary and Statistics'
      }
    },
    {
      path: '/statistics/admin',
      component: AdminStatistics,
      name: 'adminStatistics',
      meta: {
        title: 'Summary and Statistics'
      }
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
  isLoading.value = false

  const guestPagesDisable = ['profile', 'history', 'history', 'transactions', 'store', 'lobby', 'checkout', 'allUsers', 'user', 'registerAdmin', 'allTransactions', 'allGames', 'allBoards', 'personalStatistics', 'adminStatistics']
  if (guestPagesDisable.includes(to.name) && !storeAuth.user) {
    next({ name: 'home' })
    return
  }

  //after authentication, users can't access this pages
  const authPagesDisable = ['login', 'register', 'registerSuccess']
  if (authPagesDisable.includes(to.name) && storeAuth.user) {
    next({ name: 'home' })
    return
  }

  //admin users can't acess this pages
  const adminPagesDisable = ['gamemode', 'boardSelector', 'game', 'store', 'lobby', 'checkout','personalStatistics']
  if (adminPagesDisable.includes(to.name) && storeAuth.isAdmin) {
    next({ name: 'home' })
    return
  }

  //players can't acess this pages
  const playerPagesDisable = ['allUsers', 'user', 'registerAdmin', 'allTransactions', 'allGames', 'allBoards', 'adminStatistics']
  if (playerPagesDisable.includes(to.name) && storeAuth.isPlayer) {
    next({ name: 'home' })
    return
  }

  next()
})

export default router
