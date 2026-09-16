<template>
  <nav class="sticky top-0 z-50 glass border-b-0 border-white/20 dark:border-slate-700/30">
    <div class="container mx-auto px-4 py-4 flex items-center justify-between">
      <RouterLink to="/" class="text-2xl font-black bg-clip-text text-transparent bg-gradient-to-r from-fuchsia-600 to-indigo-600 dark:from-fuchsia-400 dark:to-indigo-400 tracking-tight">
        Ecommerce
      </RouterLink>

      <div class="hidden md:flex gap-8 items-center font-medium">
        <RouterLink to="/products" class="hover:text-fuchsia-600 dark:hover:text-fuchsia-400 transition-colors">Produk</RouterLink>
        <RouterLink v-if="authStore.isLoggedIn && authStore.user?.role === 'customer'" to="/wishlist" class="hover:text-fuchsia-600 dark:hover:text-fuchsia-400 transition-colors">
          <HeartIcon class="w-5 h-5 inline" />
        </RouterLink>
        <RouterLink v-if="authStore.isLoggedIn && authStore.user?.role === 'customer'" to="/orders" class="hover:text-fuchsia-600 dark:hover:text-fuchsia-400 transition-colors">Pesanan</RouterLink>
        <RouterLink v-if="authStore.isLoggedIn && authStore.user?.role === 'customer'" to="/points" class="hover:text-fuchsia-600 dark:hover:text-fuchsia-400 transition-colors">Poin</RouterLink>
        <RouterLink v-if="authStore.isLoggedIn && authStore.user?.role === 'cashier'" to="/cashier" class="hover:text-fuchsia-600 dark:hover:text-fuchsia-400 transition-colors">Kasir</RouterLink>
        <RouterLink v-if="authStore.isLoggedIn && authStore.user?.role === 'admin'" to="/admin" class="hover:text-fuchsia-600 dark:hover:text-fuchsia-400 transition-colors">Admin</RouterLink>
      </div>

      <div class="flex items-center gap-4">
        <CartBadge v-if="authStore.isLoggedIn && authStore.user?.role === 'customer'" />
        
        <button @click="toggleTheme" class="p-2 hover:bg-slate-200/50 dark:hover:bg-slate-800/50 rounded-full transition-colors backdrop-blur-md">
          <MoonIcon v-if="!isDark" class="w-5 h-5" />
          <SunIcon v-else class="w-5 h-5" />
        </button>

        <div v-if="authStore.isLoggedIn" class="flex items-center gap-3">
          <div class="hidden md:flex flex-col text-right">
            <span class="text-sm font-bold leading-none">{{ authStore.user?.name }}</span>
            <span class="text-xs text-slate-500 uppercase font-semibold">{{ authStore.user?.role }}</span>
          </div>
          <button @click="logout" class="btn-secondary text-sm !px-4 !py-1.5 border-rose-500/30 hover:border-rose-500/50 text-rose-600 dark:text-rose-400">Logout</button>
        </div>
        <div v-else class="flex gap-2">
          <RouterLink to="/login" class="btn-secondary text-sm !px-4 !py-1.5">Login</RouterLink>
          <RouterLink to="/register" class="btn-primary text-sm !px-4 !py-1.5">Daftar</RouterLink>
        </div>
      </div>
    </div>
  </nav>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { HeartIcon, MoonIcon, SunIcon } from '@heroicons/vue/24/outline'
import CartBadge from '@/components/CartBadge.vue'

const authStore = useAuthStore()
const isDark = ref(false)

const toggleTheme = () => {
  isDark.value = !isDark.value
  if (isDark.value) {
    document.documentElement.classList.add('dark')
    localStorage.setItem('theme', 'dark')
  } else {
    document.documentElement.classList.remove('dark')
    localStorage.setItem('theme', 'light')
  }
}

const logout = async () => {
  await authStore.logout()
  window.location.href = '/login'
}

onMounted(() => {
  const saved = localStorage.getItem('theme')
  if (saved === 'dark' || (!saved && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
    isDark.value = true
    document.documentElement.classList.add('dark')
  }
})
</script>
