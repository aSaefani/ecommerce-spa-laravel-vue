<template>
  <div class="min-h-screen bg-slate-50 dark:bg-slate-900">
    <Navbar />
    <main class="container mx-auto px-4 py-8">
      <RouterView />
    </main>
    <Footer />
  </div>
</template>

<script setup>
import { onMounted } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { useCartStore } from '@/stores/cart'
import Navbar from '@/components/Navbar.vue'
import Footer from '@/components/Footer.vue'

const authStore = useAuthStore()
const cartStore = useCartStore()

onMounted(() => {
  authStore.fetchUser()
  if (authStore.isLoggedIn && authStore.user?.role === 'customer') {
    cartStore.fetchCart()
  }
})
</script>
