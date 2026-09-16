<template>
  <div class="space-y-6">
    <h1 class="text-3xl font-bold">Pesanan Saya</h1>
    
    <div v-if="loading" class="space-y-4">
      <div v-for="i in 3" :key="i" class="card animate-pulse h-24"></div>
    </div>

    <div v-else-if="orders.length === 0" class="card text-center py-12">
      <p class="text-slate-600 dark:text-slate-400 mb-4">Belum ada pesanan</p>
      <RouterLink to="/products" class="btn-primary">Belanja Sekarang</RouterLink>
    </div>

    <div v-else class="space-y-4">
      <div v-for="order in orders" :key="order.id" class="card hover:bg-white/55 dark:hover:bg-slate-800/55 hover:shadow-xl">
        <div class="flex justify-between items-start mb-4">
          <div>
            <h3 class="font-bold text-lg">{{ order.order_number }}</h3>
            <p class="text-sm text-slate-600 dark:text-slate-400">{{ formatDate(order.created_at) }}</p>
          </div>
          <span :class="['badge', statusBadgeClass(order.status)]">{{ order.status }}</span>
        </div>
        <div class="flex justify-between items-center">
          <span class="text-lg font-bold">Rp{{ formatPrice(order.total) }}</span>
          <button @click="viewOrder(order)" class="btn-primary text-sm">Lihat Detail</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import api from '@/services/api'

const orders = ref([])
const loading = ref(true)

const formatPrice = (price) => {
  return new Intl.NumberFormat('id-ID').format(price)
}

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('id-ID')
}

const statusBadgeClass = (status) => {
  const classes = {
    pending: 'badge-warning',
    paid: 'badge-success',
    completed: 'badge-success',
    cancelled: 'badge-danger',
  }
  return classes[status] || 'badge-secondary'
}

const viewOrder = (order) => {
  window.location.href = `/orders/${order.id}`
}

const fetchOrders = async () => {
  try {
    loading.value = true
    const response = await api.get('/api/orders')
    orders.value = response.data
  } catch (error) {
    console.error('Failed to fetch orders', error)
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  fetchOrders()
})
</script>
