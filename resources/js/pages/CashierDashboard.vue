<template>
  <div class="space-y-6">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
      <div>
        <h1 class="text-3xl font-bold">Dashboard Kasir</h1>
        <p class="text-slate-500 text-sm">Kelola dan konfirmasi pembayaran secara real-time.</p>
      </div>
      <div class="flex items-center gap-2 text-sm">
        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
        <span class="text-slate-600 dark:text-slate-400">Live Monitoring Aktif</span>
      </div>
    </div>

    <!-- Metrics -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
      <div class="card border-l-4 border-amber-500">
        <p class="text-xs uppercase tracking-wider text-slate-500 font-semibold">Menunggu Pembayaran</p>
        <p class="text-3xl font-bold mt-2 text-amber-600">{{ stats.pendingOrders }}</p>
      </div>
      <div class="card border-l-4 border-blue-500">
        <p class="text-xs uppercase tracking-wider text-slate-500 font-semibold">Total Pesanan Hari Ini</p>
        <p class="text-3xl font-bold mt-2 text-blue-600">{{ stats.todayOrders }}</p>
      </div>
      <div class="card border-l-4 border-emerald-500">
        <p class="text-xs uppercase tracking-wider text-slate-500 font-semibold">Pendapatan Hari Ini</p>
        <p class="text-3xl font-bold mt-2 text-emerald-600">Rp{{ formatPrice(stats.todayRevenue) }}</p>
      </div>
    </div>

    <!-- Order Filter -->
    <div class="flex gap-2">
      <button 
        v-for="f in filters" 
        :key="f.id"
        @click="filter = f.id"
        :class="[
          'px-4 py-1.5 rounded-full text-sm font-medium transition-all',
          filter === f.id ? 'bg-slate-800 dark:bg-slate-100 text-white dark:text-slate-900' : 'bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-100'
        ]"
      >
        {{ f.name }}
      </button>
    </div>

    <!-- Orders List -->
    <div class="card overflow-x-auto">
      <div v-if="loading" class="p-8 text-center text-slate-500">Memuat data pesanan...</div>
      <table v-else class="w-full text-left text-sm">
        <thead class="bg-slate-50 dark:bg-slate-700/50">
          <tr>
            <th class="p-3">Order #</th>
            <th class="p-3">Pelanggan</th>
            <th class="p-3">Total</th>
            <th class="p-3">Status</th>
            <th class="p-3">Waktu</th>
            <th class="p-3 text-right">Aksi</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
          <tr v-for="order in filteredOrders" :key="order.id" class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
            <td class="p-3 font-mono font-medium">{{ order.order_number }}</td>
            <td class="p-3">{{ order.user?.name || 'Guest' }}</td>
            <td class="p-3 font-bold text-orange-600">Rp{{ formatPrice(order.total) }}</td>
            <td class="p-3">
              <span :class="statusBadge(order.status)">{{ order.status }}</span>
            </td>
            <td class="p-3 text-slate-500 text-xs">{{ formatDate(order.created_at) }}</td>
            <td class="p-3 text-right">
              <button 
                v-if="order.status === 'pending'"
                @click="confirmPayment(order)"
                :disabled="processing === order.id"
                class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-lg transition-all disabled:opacity-50"
              >
                {{ processing === order.id ? 'Memproses...' : 'Konfirmasi Bayar' }}
              </button>
              <span v-else class="text-xs text-slate-400">Selesai</span>
            </td>
          </tr>
          <tr v-if="filteredOrders.length === 0">
            <td colspan="6" class="p-8 text-center text-slate-500">Tidak ada pesanan.</td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Toast Notification -->
    <Teleport to="body">
      <Transition name="toast">
        <div v-if="toast" class="fixed bottom-6 right-6 bg-emerald-600 text-white px-6 py-3 rounded-lg shadow-xl z-50 flex items-center gap-3">
          <span class="text-lg">✓</span>
          <span class="text-sm font-medium">{{ toast }}</span>
        </div>
      </Transition>
    </Teleport>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted, onUnmounted } from 'vue'
import api from '@/services/api'

const loading = ref(true)
const processing = ref(null)
const toast = ref('')
const filter = ref('pending')
const orders = ref([])
const stats = reactive({ pendingOrders: 0, todayOrders: 0, todayRevenue: 0 })
let pollInterval = null

const filters = [
  { id: 'pending', name: 'Menunggu' },
  { id: 'paid', name: 'Dibayar' },
  { id: 'all', name: 'Semua' },
]

const filteredOrders = computed(() => {
  if (filter.value === 'all') return orders.value
  return orders.value.filter(o => o.status === filter.value)
})

const formatPrice = (val) => new Intl.NumberFormat('id-ID').format(val || 0)
const formatDate = (val) => new Date(val).toLocaleString('id-ID', { dateStyle: 'short', timeStyle: 'short' })
const statusBadge = (s) => ({
  paid: 'badge badge-success text-xs',
  pending: 'badge badge-warning text-xs animate-pulse',
  completed: 'badge badge-success text-xs',
  cancelled: 'badge badge-danger text-xs'
}[s] || 'badge badge-secondary text-xs')

const showToast = (msg) => {
  toast.value = msg
  setTimeout(() => { toast.value = '' }, 3000)
}

const fetchData = async () => {
  try {
    const [statsRes, ordersRes] = await Promise.all([
      api.get('/api/cashier/dashboard'),
      api.get('/api/cashier/orders'),
    ])
    Object.assign(stats, statsRes.data)
    orders.value = ordersRes.data.data || ordersRes.data
  } catch (e) {
    console.error(e)
  } finally {
    loading.value = false
  }
}

const confirmPayment = async (order) => {
  try {
    processing.value = order.id
    await api.post(`/api/cashier/orders/${order.id}/confirm-payment`)
    showToast(`Pembayaran ${order.order_number} berhasil dikonfirmasi!`)
    await fetchData()
  } catch (e) {
    console.error(e)
  } finally {
    processing.value = null
  }
}

onMounted(() => {
  fetchData()
  pollInterval = setInterval(fetchData, 10000)
})

onUnmounted(() => {
  clearInterval(pollInterval)
})
</script>

<style scoped>
.toast-enter-active, .toast-leave-active { transition: all 0.3s ease; }
.toast-enter-from, .toast-leave-to { opacity: 0; transform: translateY(20px); }
</style>
