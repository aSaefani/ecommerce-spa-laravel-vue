import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import api from '@/services/api'

export const useCartStore = defineStore('cart', () => {
  const items = ref([])
  const loading = ref(false)

  const total = computed(() => items.value.reduce((sum, item) => sum + (item.product.price * item.quantity), 0))
  const count = computed(() => items.value.reduce((sum, item) => sum + item.quantity, 0))

  const fetchCart = async () => {
    try {
      loading.value = true
      const response = await api.get('/api/cart')
      items.value = response.data.items || []
    } catch (error) {
      console.error('Failed to fetch cart', error)
    } finally {
      loading.value = false
    }
  }

  const addItem = async (productId, quantity) => {
    try {
      await api.post('/api/cart/add', { product_id: productId, quantity })
      await fetchCart()
    } catch (error) {
      throw error
    }
  }

  const removeItem = async (cartItemId) => {
    try {
      await api.delete(`/api/cart/remove/${cartItemId}`)
      await fetchCart()
    } catch (error) {
      throw error
    }
  }

  const updateQuantity = async (cartItemId, quantity) => {
    try {
      await api.post(`/api/cart/update/${cartItemId}`, { quantity })
      await fetchCart()
    } catch (error) {
      throw error
    }
  }

  const clearCart = () => {
    items.value = []
  }

  return { items, total, count, loading, fetchCart, addItem, removeItem, updateQuantity, clearCart }
})
