<template>
  <div class="space-y-8">
    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
      <div>
        <p class="text-sm font-semibold uppercase tracking-wide text-orange-600">Dashboard pelanggan</p>
        <h1 class="text-3xl font-bold">Halo, {{ authStore.user?.name || 'Pelanggan' }}</h1>
      </div>
      <RouterLink to="/products" class="btn-primary">Mulai Belanja</RouterLink>
    </div>

    <div v-if="loading" class="grid gap-4 md:grid-cols-3">
      <div v-for="index in 3" :key="index" class="card h-32 animate-pulse bg-white/20 dark:bg-slate-700/20"></div>
    </div>

    <template v-else>
      <div class="grid gap-4 md:grid-cols-3">
        <RouterLink to="/points" class="card group hover:-translate-y-1 hover:shadow-xl">
          <p class="text-sm text-slate-500 dark:text-slate-400">Saldo poin</p>
          <p class="mt-2 text-4xl font-bold text-orange-600">{{ data.balance }}</p>
          <p class="mt-3 text-sm font-medium text-orange-600">Lihat riwayat poin</p>
        </RouterLink>
        <RouterLink to="/orders" class="card group hover:-translate-y-1 hover:shadow-xl">
          <p class="text-sm text-slate-500 dark:text-slate-400">Total pesanan</p>
          <p class="mt-2 text-4xl font-bold">{{ data.totalOrders }}</p>
          <p class="mt-3 text-sm font-medium text-orange-600">Lihat semua pesanan</p>
        </RouterLink>
        <div class="card">
          <p class="text-sm text-slate-500 dark:text-slate-400">Total belanja</p>
          <p class="mt-2 text-3xl font-bold text-emerald-600">Rp{{ price(data.totalSpent) }}</p>
          <p class="mt-3 text-sm text-slate-500 dark:text-slate-400">Dari pesanan telah dibayar</p>
        </div>
      </div>

      <section class="card">
        <div class="mb-5 flex items-center justify-between">
          <h2 class="text-xl font-bold">Pesanan terbaru</h2>
          <RouterLink to="/orders" class="text-sm font-semibold text-orange-600">Lihat semua</RouterLink>
        </div>
        <div v-if="!data.recentOrders.length" class="py-8 text-center text-slate-500">Belum ada pesanan.</div>
        <div v-else class="space-y-3">
          <div v-for="order in data.recentOrders" :key="order.id" class="glass hover:bg-white/50 dark:hover:bg-slate-800/50 flex flex-col gap-3 rounded-xl border border-white/30 p-4 dark:border-slate-700/50 sm:flex-row sm:items-center sm:justify-between transition-colors">
            <div>
              <p class="font-semibold">{{ order.order_number }}</p>
              <p class="text-sm text-slate-500">{{ date(order.created_at) }}</p>
            </div>
            <div class="flex items-center gap-4">
              <span class="font-bold">Rp{{ price(order.total) }}</span>
              <span :class="badge(order.status)">{{ order.status }}</span>
            </div>
          </div>
        </div>
      </section>
    </template>
  </div>
</template>

<script setup>
import { onMounted, reactive, ref } from 'vue'
import { useAuthStore } from '@/stores/auth'
import api from '@/services/api'

const authStore = useAuthStore()
const loading = ref(true)
const data = reactive({ balance: 0, totalOrders: 0, totalSpent: 0, recentOrders: [] })
const price = value => new Intl.NumberFormat('id-ID').format(value || 0)
const date = value => new Date(value).toLocaleDateString('id-ID', { dateStyle: 'medium' })
const badge = status => ({ pending: 'badge badge-warning', paid: 'badge badge-success', completed: 'badge badge-success', cancelled: 'badge badge-danger' }[status] || 'badge badge-secondary')

onMounted(async () => {
  try {
    const response = await api.get('/api/customer/dashboard')
    Object.assign(data, response.data)
  } finally {
    loading.value = false
  }
})
</script>
