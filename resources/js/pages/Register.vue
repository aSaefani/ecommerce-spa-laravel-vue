<template>
  <div class="flex items-center justify-center min-h-[70vh]">
    <div class="glass-heavy rounded-3xl p-8 md:p-12 w-full max-w-md relative overflow-hidden">
      <!-- Decor -->
      <div class="absolute top-0 -left-10 w-32 h-32 bg-teal-500 rounded-full mix-blend-multiply filter blur-2xl opacity-40"></div>
      <div class="absolute bottom-0 -right-10 w-32 h-32 bg-indigo-500 rounded-full mix-blend-multiply filter blur-2xl opacity-40"></div>
      
      <div class="relative z-10">
        <div class="text-center mb-8">
          <h2 class="text-3xl font-black bg-clip-text text-transparent bg-gradient-to-r from-teal-600 to-indigo-600 dark:from-teal-400 dark:to-indigo-400">Buat Akun</h2>
          <p class="text-slate-500 dark:text-slate-400 mt-2 text-sm">Daftar untuk mulai berbelanja</p>
        </div>

        <form @submit.prevent="handleRegister" class="space-y-4">
          <div v-if="error" class="bg-rose-500/20 text-rose-600 border border-rose-500/50 px-4 py-3 rounded-xl text-sm backdrop-blur-sm">
            {{ error }}
          </div>

          <div>
            <label class="block text-xs font-bold uppercase tracking-wider mb-2 text-slate-500">Nama Lengkap</label>
            <input v-model="form.name" type="text" class="input-field" placeholder="Budi Santoso" required />
            <span v-if="errors.name" class="text-xs text-rose-500 mt-1">{{ errors.name[0] }}</span>
          </div>
          <div>
            <label class="block text-xs font-bold uppercase tracking-wider mb-2 text-slate-500">Email</label>
            <input v-model="form.email" type="email" class="input-field" placeholder="nama@email.com" required />
            <span v-if="errors.email" class="text-xs text-rose-500 mt-1">{{ errors.email[0] }}</span>
          </div>
          <div>
            <label class="block text-xs font-bold uppercase tracking-wider mb-2 text-slate-500">Password</label>
            <input v-model="form.password" type="password" class="input-field" placeholder="Minimal 8 karakter" required />
            <span v-if="errors.password" class="text-xs text-rose-500 mt-1">{{ errors.password[0] }}</span>
          </div>
          <div>
            <label class="block text-xs font-bold uppercase tracking-wider mb-2 text-slate-500">Konfirmasi Password</label>
            <input v-model="form.password_confirmation" type="password" class="input-field" placeholder="Ulangi password" required />
          </div>

          <button type="submit" :disabled="loading" class="btn-primary w-full mt-6 h-12 text-lg">
            <span v-if="!loading">Daftar Sekarang</span>
            <span v-else class="animate-pulse">Memproses...</span>
          </button>
        </form>

        <div class="text-center mt-6">
          <p class="text-sm text-slate-500">
            Sudah punya akun? 
            <RouterLink to="/login" class="font-bold text-teal-600 hover:text-teal-500 transition-colors">Login di sini</RouterLink>
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

const form = reactive({ name: '', email: '', password: '', password_confirmation: '' })
const error = ref('')
const errors = ref({})
const loading = ref(false)

const handleRegister = async () => {
  try {
    loading.value = true
    error.value = ''
    errors.value = {}
    
    await api.get('/sanctum/csrf-cookie')
    await api.post('/register', form)
    await authStore.fetchUser()
    
    router.push('/dashboard')
  } catch (err) {
    if (err.response?.status === 422) {
      errors.value = err.response.data.errors
    } else {
      error.value = 'Terjadi kesalahan sistem.'
    }
  } finally {
    loading.value = false
  }
}
</script>
