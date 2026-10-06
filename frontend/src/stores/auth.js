import { computed, ref } from 'vue'
import { defineStore } from 'pinia'
import { api } from '../api/client'
import router from '../router'

export const useAuthStore = defineStore('auth', () => {
  const user = ref(JSON.parse(localStorage.getItem('user') || 'null'))
  const isAuthenticated = computed(() => Boolean(localStorage.getItem('token')))

  async function login(credentials) {
    const { data } = await api.post('/login', credentials)
    user.value = data.user
    localStorage.setItem('token', data.token)
    localStorage.setItem('user', JSON.stringify(data.user))
    await router.push('/')
  }

  async function logout() {
    try { await api.post('/logout') } finally {
      user.value = null
      localStorage.removeItem('token')
      localStorage.removeItem('user')
      await router.push('/login')
    }
  }

  return { user, isAuthenticated, login, logout }
})

