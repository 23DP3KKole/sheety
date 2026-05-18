import { defineStore } from 'pinia'
import { computed, ref } from 'vue'
import api from '../api/client'

export const useAuthStore = defineStore('auth', () => {
  const user = ref(null)
  const token = ref(localStorage.getItem('token') || '')
  const loading = ref(false)

  const isAuthenticated = computed(() => !!token.value)
  const isAdmin = computed(() => user.value?.is_admin === true)

  async function fetchUser() {
    if (!token.value) return
    const { data } = await api.get('/me')
    user.value = data.user
  }

  async function login(credentials) {
    loading.value = true
    try {
      const { data } = await api.post('/login', credentials)
      setSession(data.token, data.user)
    } finally {
      loading.value = false
    }
  }

  async function register(payload) {
    loading.value = true
    try {
      const { data } = await api.post('/register', payload)
      setSession(data.token, data.user)
    } finally {
      loading.value = false
    }
  }

  async function logout() {
    try {
      if (token.value) await api.post('/logout')
    } catch {
      // ignore
    }
    clearSession()
  }

  function setSession(newToken, newUser) {
    token.value = newToken
    user.value = newUser
    localStorage.setItem('token', newToken)
  }

  function clearSession() {
    token.value = ''
    user.value = null
    localStorage.removeItem('token')
  }

  async function init() {
    if (token.value) {
      try {
        await fetchUser()
      } catch {
        clearSession()
      }
    }
  }

  return {
    user,
    token,
    loading,
    isAuthenticated,
    isAdmin,
    login,
    register,
    logout,
    fetchUser,
    init,
  }
})
