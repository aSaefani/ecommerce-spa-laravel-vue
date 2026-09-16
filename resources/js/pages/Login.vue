<template>
  <div class="flex items-center justify-center min-h-[70vh]">
    <div class="glass-heavy rounded-3xl p-8 md:p-12 w-full max-w-md relative overflow-hidden">
      <!-- Decor -->
      <div class="absolute -top-10 -right-10 w-32 h-32 bg-fuchsia-500 rounded-full mix-blend-multiply filter blur-2xl opacity-50"></div>
      <div class="absolute -bottom-10 -left-10 w-32 h-32 bg-indigo-500 rounded-full mix-blend-multiply filter blur-2xl opacity-50"></div>
      
      <div class="relative z-10">
        <div class="text-center mb-8">
          <h2 class="text-3xl font-black bg-clip-text text-transparent bg-gradient-to-r from-fuchsia-600 to-indigo-600 dark:from-fuchsia-400 dark:to-indigo-400">Selamat Datang</h2>
          <p class="text-slate-500 dark:text-slate-400 mt-2 text-sm">Silakan login untuk melanjutkan</p>
        </div>

        <form @submit.prevent="handleLogin" class="space-y-5">
          <div v-if="error" class="bg-rose-500/20 text-rose-600 border border-rose-500/50 px-4 py-3 rounded-xl text-sm backdrop-blur-sm">
            {{ error }}
          </div>

          <div>
            <label class="block text-xs font-bold uppercase tracking-wider mb-2 text-slate-500">Email</label>
            <input v-model="form.email" type="email" class="input-field" placeholder="nama@email.com" required />
          </div>
          <div>
            <label class="block text-xs font-bold uppercase tracking-wider mb-2 text-slate-500">Password</label>
            <input v-model="form.password" type="password" class="input-field" placeholder="••••••••" required />
          </div>

          <button type="submit" :disabled="loading" class="btn-primary w-full mt-4 h-12 text-lg">
            <span v-if="!loading">Masuk</span>
            <span v-else class="animate-pulse">Memproses...</span>
          </button>
        </form>

        <div class="text-center mt-6">
          <p class="text-sm text-slate-500">
            Belum punya akun? 
            <RouterLink to="/register" class="font-bold text-fuchsia-600 hover:text-fuchsia-500 transition-colors">Daftar sekarang</RouterLink>
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { reactive, ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import api from '@/services/api'

const router = useRouter()
const authStore = useAuthStore()

const form = reactive({ email: '', password: '' })
const error = ref('')
const loading = ref(false)

const handleLogin = async () => {
  try {
    loading.value = true
    error.value = ''
    await api.get('/sanctum/csrf-cookie')
    await api.post('/login', form)
    await authStore.fetchUser()
    
    if (authStore.user?.role === 'admin') router.push('/admin')
    else if (authStore.user?.role === 'cashier') router.push('/cashier')
    else router.push('/dashboard')
  } catch (err) {
    error.value = 'Email atau password salah.'
  } finally {
    loading.value = false
  }
}
</script>
