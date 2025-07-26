import { defineStore } from 'pinia'
import api from '@/api/axios'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: JSON.parse(localStorage.getItem('user')) || null,
    token: localStorage.getItem('auth_token') || null,
    loading: false,
  }),

  getters: {
    isAuthenticated: (state) => !!state.token,
    isWriter: (state) => state.user?.roles?.includes('writer'),
    isEditor: (state) => state.user?.roles?.includes('editor'),
  },

  actions: {
    async login(credentials) {
      this.loading = true
      try {
        const response = await api.post('/login', credentials)

        this.token = response.data.token
        this.user = response.data.user

        localStorage.setItem('auth_token', this.token)
        localStorage.setItem('user', JSON.stringify(this.user))

        return response.data
      } catch (error) {
        throw error
      } finally {
        this.loading = false
      }
    },

    async register(userData) {
      this.loading = true
      try {
        const response = await api.post('/register', userData)

        this.token = response.data.token
        this.user = response.data.user

        localStorage.setItem('auth_token', this.token)
        localStorage.setItem('user', JSON.stringify(this.user))

        return response.data
      } catch (error) {
        throw error
      } finally {
        this.loading = false
      }
    },

    async logout() {
      try {
        await api.post('/logout')
      } catch (error) {
        console.error('Logout error:', error)
      } finally {
        this.token = null
        this.user = null
        localStorage.removeItem('auth_token')
        localStorage.removeItem('user')
      }
    },

    async fetchUser() {
      try {
        const response = await api.get('/me')
        this.user = response.data.user
        localStorage.setItem('user', JSON.stringify(this.user))
      } catch (error) {
        this.logout()
        throw error
      }
    },
  },
})
