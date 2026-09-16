import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import api from '@/services/api'

export const useProductStore = defineStore('products', () => {
  const products = ref([])
  const categories = ref([])
  const loading = ref(false)
  const selectedCategory = ref(null)
  const searchQuery = ref('')
  const priceRange = ref([0, 1000000])

  const filteredProducts = computed(() => {
    return products.value.filter(p => {
      const matchCategory = !selectedCategory.value || p.category_id === selectedCategory.value
      const matchSearch = !searchQuery.value || p.name.toLowerCase().includes(searchQuery.value.toLowerCase())
      const matchPrice = p.price >= priceRange.value[0] && p.price <= priceRange.value[1]
      return matchCategory && matchSearch && matchPrice
    })
  })

  const fetchProducts = async () => {
    try {
      loading.value = true
      const response = await api.get('/api/products')
      products.value = response.data
    } catch (error) {
      console.error('Failed to fetch products', error)
    } finally {
      loading.value = false
    }
  }

  const fetchCategories = async () => {
    try {
      const response = await api.get('/api/categories')
      categories.value = response.data
    } catch (error) {
      console.error('Failed to fetch categories', error)
    }
  }

  return {
    products,
    categories,
    loading,
    selectedCategory,
    searchQuery,
    priceRange,
    filteredProducts,
    fetchProducts,
    fetchCategories,
  }
})
