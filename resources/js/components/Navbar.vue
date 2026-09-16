<template>
  <nav class="bg-slate-900 dark:bg-slate-950 text-white shadow-lg sticky top-0 z-50">
    <div class="container mx-auto px-4 py-4 flex items-center justify-between">
      <RouterLink to="/" class="text-2xl font-bold text-orange-500">Ecommerce</RouterLink>

      <div class="hidden md:flex gap-8 items-center">
        <RouterLink to="/products" class="hover:text-orange-400 transition">Produk</RouterLink>
        <RouterLink v-if="authStore.isLoggedIn && authStore.user?.role === 'customer'" to="/wishlist" class="hover:text-orange-400 transition">
          <HeartIcon class="w-5 h-5 inline" />
        </RouterLink>
        <RouterLink v-if="authStore.isLoggedIn && authStore.user?.role === 'customer'" to="/orders" class="hover:text-orange-400 transition">Pesanan</RouterLink>
        <RouterLink v-if="authStore.isLoggedIn && authStore.user?.role === 'customer'" to="/points" class="hover:text-orange-400 transition">Poin</RouterLink>
        <RouterLink v-if="authStore.isLoggedIn && authStore.user?.role === 'cashier'" to="/cashier" class="hover:text-orange-400 transition">Kasir</RouterLink>
        <RouterLink v-if="authStore.isLoggedIn && authStore.user?.role === 'admin'" to="/admin" class="hover:text-orange-400 transition">Admin</RouterLink>
      </div>

      <div class="flex items-center gap-4">
        <CartBadge v-if="authStore.isLoggedIn && authStore.user?.role === 'customer'" />
        <button @click="toggleTheme" class="p-2 hover:bg-slate-800 rounded">
          <MoonIcon v-if="!isDark" class="w-5 h-5" />
          <SunIcon v-else class="w-5 h-5" />
        </button>

        <div v-if="authStore.isLoggedIn" class="flex items-center gap-2">
          <span class="text-sm">{{ authStore.user?.name }}</span>
          <button @click="logout" class="btn-primary text-sm">Logout</button>
        </div>
        <div v-else class="flex gap-2">
          <RouterLink to="/login" class="btn-secondary text-sm">Login</RouterLink>
          <RouterLink to="/register" class="btn-primary text-sm">Register</RouterLink>
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
