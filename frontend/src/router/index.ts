import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'dashboard',
      component: HomeView,
      meta: {
        title: 'Navlogo Dashboard',
      }
    },
  ],
})

// Navigation guards
router.beforeEach((to, from, next) => {
  // Set page title
  document.title = to.meta.title as string || 'Navlogo'
  next()
})

export default router
