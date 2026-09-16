import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import api from '@/services/api'

export const useAuthStore = defineStore('auth', () => {
  const user = ref(null)
  const isLoggedIn = computed(() => !!user.value)

  const fetchUser = async () => {
    try {
      const response = await api.get('/api/user')
      user.value = response.data
    } catch (error) {
      user.value = null
    }
  }

  const logout = async () => {
    await api.post('/logout')
    user.value = null
  }

  return { user, isLoggedIn, fetchUser, logout }
})
