<template>
  <div class="space-y-6">
    <h1 class="text-3xl font-bold">Wishlist Saya</h1>
    
    <div v-if="loading" class="grid md:grid-cols-4 gap-4">
      <div v-for="i in 8" :key="i" class="card animate-pulse h-64"></div>
    </div>

    <div v-else-if="items.length === 0" class="card text-center py-12">
      <p class="text-slate-600 dark:text-slate-400 mb-4">Wishlist kosong</p>
      <RouterLink to="/products" class="btn-primary">Lihat Produk</RouterLink>
    </div>

    <div v-else class="grid md:grid-cols-4 gap-4">
      <div v-for="item in items" :key="item.id" class="card hover:shadow-lg transition-shadow">
        <div class="h-48 bg-slate-200 dark:bg-slate-700 rounded-lg mb-4">
          <img v-if="item.product.image" :src="`/storage/${item.product.image}`" :alt="item.product.name" class="w-full h-full object-cover rounded-lg" />
        </div>
        <h3 class="font-bold truncate mb-1">{{ item.product.name }}</h3>
        <p class="text-orange-600 font-bold mb-4">Rp{{ formatPrice(item.product.price) }}</p>
        <div class="flex gap-2">
          <button @click="addToCart(item.product)" class="flex-1 btn-primary text-sm">Beli</button>
          <button @click="removeFromWishlist(item.id)" class="flex-1 btn-secondary text-sm">Hapus</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useCartStore } from '@/stores/cart'
import api from '@/services/api'

const items = ref([])
const loading = ref(true)
const cartStore = useCartStore()

const formatPrice = (price) => {
  return new Intl.NumberFormat('id-ID').format(price)
}

const fetchWishlist = async () => {
  try {
    loading.value = true
    const response = await api.get('/api/wishlist')
    items.value = response.data
  } catch (error) {
    console.error('Failed to fetch wishlist', error)
  } finally {
    loading.value = false
  }
}

const removeFromWishlist = async (id) => {
  try {
    await api.delete(`/api/wishlist/${id}`)
    items.value = items.value.filter(item => item.id !== id)
  } catch (error) {
    console.error('Failed to remove from wishlist', error)
  }
}

const addToCart = async (product) => {
  try {
    await cartStore.addItem(product.id, 1)
  } catch (error) {
    console.error('Failed to add to cart', error)
  }
}

onMounted(() => {
  fetchWishlist()
})
</script>
