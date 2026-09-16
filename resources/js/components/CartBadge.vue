<template>
  <button @click="cartOpen = !cartOpen" class="relative hover:scale-110 transition-transform">
    <ShoppingCartIcon class="w-6 h-6 text-orange-400 hover:text-orange-500" />
    <span v-if="cartStore.count > 0" class="absolute -top-2 -right-2 bg-red-500 text-white text-xs font-bold rounded-full w-5 h-5 flex items-center justify-center animate-bounce">
      {{ cartStore.count }}
    </span>
  </button>

  <CartDrawer v-model="cartOpen" />
</template>

<script setup>
import { ref } from 'vue'
import { ShoppingCartIcon } from '@heroicons/vue/24/outline'
import { useCartStore } from '@/stores/cart'
import CartDrawer from '@/components/CartDrawer.vue'

const cartStore = useCartStore()
const cartOpen = ref(false)
</script>

