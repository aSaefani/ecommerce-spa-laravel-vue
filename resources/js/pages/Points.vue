<template>
  <div class="space-y-6">
    <h1 class="text-3xl font-bold">Poin Reward Saya</h1>
    
    <div class="card hover:bg-white/55 dark:hover:bg-slate-800/55">
      <div class="text-center">
        <p class="text-slate-600 dark:text-slate-400 mb-2">Saldo Poin</p>
        <h2 class="text-5xl font-bold text-orange-600">{{ balance }}</h2>
      </div>
    </div>

    <div>
      <h3 class="text-xl font-bold mb-4">Riwayat Poin</h3>
      <div v-if="history.length === 0" class="card text-center py-8 text-slate-600 dark:text-slate-400">
        Belum ada riwayat poin
      </div>
      <div v-else class="space-y-3">
        <div v-for="log in history" :key="log.id" class="card flex justify-between items-center">
          <div>
            <p class="font-bold">{{ log.description }}</p>
            <p class="text-sm text-slate-600 dark:text-slate-400">{{ formatDate(log.created_at) }}</p>
          </div>
          <div class="text-right">
            <span :class="['font-bold text-lg', log.type === 'earn' ? 'text-green-600' : 'text-red-600']">
              {{ log.type === 'earn' ? '+' : '-' }}{{ log.points }}
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import api from '@/services/api'

const balance = ref(0)
const history = ref([])

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('id-ID')
}

const fetchPoints = async () => {
  try {
    const response = await api.get('/api/points')
    balance.value = response.data.balance
    history.value = response.data.history
  } catch (error) {
    console.error('Failed to fetch points', error)
  }
}

onMounted(() => {
  fetchPoints()
})
</script>
