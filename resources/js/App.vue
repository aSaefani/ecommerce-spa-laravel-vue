<template>
  <div class="relative min-h-screen bg-slate-50 dark:bg-slate-950 overflow-hidden font-sans text-slate-800 dark:text-slate-100 transition-colors duration-500">
    
    <!-- BACKGROUND AURORA GRADIENT & BLOBS -->
    <div class="fixed inset-0 z-0 overflow-hidden pointer-events-none">
      <div class="absolute inset-0 bg-gradient-to-br from-indigo-100 via-fuchsia-50 to-teal-50 dark:from-indigo-950 dark:via-slate-900 dark:to-teal-950 opacity-80"></div>
      
      <!-- Animated Blobs -->
      <div class="absolute top-0 -left-4 w-72 h-72 bg-fuchsia-400 dark:bg-fuchsia-600 rounded-full mix-blend-multiply dark:mix-blend-screen filter blur-3xl opacity-30 animate-blob"></div>
      <div class="absolute top-0 -right-4 w-72 h-72 bg-indigo-400 dark:bg-indigo-600 rounded-full mix-blend-multiply dark:mix-blend-screen filter blur-3xl opacity-30 animate-blob animation-delay-2000"></div>
      <div class="absolute -bottom-8 left-20 w-72 h-72 bg-teal-400 dark:bg-teal-600 rounded-full mix-blend-multiply dark:mix-blend-screen filter blur-3xl opacity-30 animate-blob-reverse animation-delay-4000"></div>
    </div>

    <!-- MAIN CONTENT APP -->
    <div class="relative z-10 flex flex-col min-h-screen">
      <Navbar />
      <main class="container mx-auto px-4 py-10 flex-grow">
        <RouterView v-slot="{ Component }">
          <Transition name="page" mode="out-in">
            <component :is="Component" />
          </Transition>
        </RouterView>
      </main>
      <Footer />
    </div>

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

<style>
.page-enter-active,
.page-leave-active {
  transition: opacity 0.3s ease, transform 0.3s ease;
}
.page-enter-from {
  opacity: 0;
  transform: translateY(10px);
}
.page-leave-to {
  opacity: 0;
  transform: translateY(-10px);
}
.animation-delay-2000 {
  animation-delay: 2s;
}
.animation-delay-4000 {
  animation-delay: 4s;
}
</style>
