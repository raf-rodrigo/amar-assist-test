import { createRouter, createWebHistory } from 'vue-router'
import LoginView from '../views/LoginView.vue'
import DashboardView from '../views/DashboardView.vue'
import CategoriesView from '../views/CategoriesView.vue'
import EntriesView from '../views/EntriesView.vue'
import ProfileView from '../views/ProfileView.vue'
import SettingsView from '../views/SettingsView.vue'

const router = createRouter({
  history: createWebHistory(),
  routes: [
    { path: '/login', component: LoginView, meta: { guest: true } },
    { path: '/', component: DashboardView },
    { path: '/categories', component: CategoriesView },
    { path: '/incomes', component: EntriesView, props: { type: 'incomes' } },
    { path: '/expenses', component: EntriesView, props: { type: 'expenses' } },
    { path: '/profile', component: ProfileView },
    { path: '/settings', component: SettingsView },
  ],
})

router.beforeEach((to) => {
  const authenticated = Boolean(localStorage.getItem('token'))
  if (!to.meta.guest && !authenticated) return '/login'
  if (to.meta.guest && authenticated) return '/'
})

export default router
