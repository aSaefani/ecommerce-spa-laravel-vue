<template>
  <Teleport to="body">
    <Transition name="fade">
      <div v-if="isOpen" class="fixed inset-0 bg-black bg-opacity-50 z-40" @click="close"></div>
    </Transition>

    <Transition name="slide-right">
      <div v-if="isOpen" class="fixed right-0 top-0 h-full w-full md:w-96 bg-white dark:bg-slate-800 shadow-xl z-50 flex flex-col">
        <!-- Header -->
        <div class="sticky top-0 bg-white dark:bg-slate-800 border-b border-slate-200 dark:border-slate-700 p-6 flex justify-between items-center">
          <h2 class="text-2xl font-bold">Keranjang</h2>
          <button @click="close" class="text-slate-500 hover:text-slate-700 dark:hover:text-slate-300">
            <XMarkIcon class="w-6 h-6" />
          </button>
        </div>

        <!-- Items List -->
        <div class="flex-1 overflow-y-auto p-6 space-y-4">
          <div v-if="cartStore.items.length === 0" class="text-center py-12 text-slate-600 dark:text-slate-400">
            <ShoppingCartIcon class="w-12 h-12 mx-auto mb-4 opacity-50" />
            <p>Keranjang kosong</p>
          </div>

          <div v-else>
            <div 
              v-for="item in cartStore.items" 
              :key="item.id"
              class="card p-4 flex gap-4 hover:shadow-md transition-shadow"
            >
              <!-- Product Image -->
              <div class="w-20 h-20 bg-slate-200 dark:bg-slate-700 rounded-lg overflow-hidden flex-shrink-0">
                <img 
                  v-if="item.product.image"
                  :src="`/storage/${item.product.image}`"
                  :alt="item.product.name"
                  class="w-full h-full object-cover"
                />
              </div>

              <!-- Product Info -->
              <div class="flex-1 min-w-0">
                <h3 class="font-bold truncate">{{ item.product.name }}</h3>
                <p class="text-sm text-orange-600 font-bold">Rp{{ formatPrice(item.product.price) }}</p>
                
                <!-- Quantity Adjuster -->
                <div class="flex items-center gap-2 mt-2">
                  <button 
                    @click="decrementQuantity(item)"
                    class="w-6 h-6 rounded border border-slate-300 dark:border-slate-600 hover:bg-slate-100 dark:hover:bg-slate-700 flex items-center justify-center"
                  >
                    −
                  </button>
                  <input 
                    :value="item.quantity"
                    type="number"
                    class="w-12 text-center border border-slate-300 dark:border-slate-600 rounded bg-white dark:bg-slate-700"
                    @change="e => updateQuantity(item, parseInt(e.target.value))"
                  />
                  <button 
                    @click="incrementQuantity(item)"
                    class="w-6 h-6 rounded border border-slate-300 dark:border-slate-600 hover:bg-slate-100 dark:hover:bg-slate-700 flex items-center justify-center"
                  >
                    +
                  </button>
                  <button 
                    @click="removeItem(item.id)"
                    class="ml-auto text-red-500 hover:text-red-600"
                  >
                    <TrashIcon class="w-4 h-4" />
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Summary & Checkout -->
        <div v-if="cartStore.items.length > 0" class="sticky bottom-0 bg-white dark:bg-slate-800 border-t border-slate-200 dark:border-slate-700 p-6 space-y-4">
          <div class="space-y-2 text-sm">
            <div class="flex justify-between">
              <span>Subtotal:</span>
              <span>Rp{{ formatPrice(cartStore.total) }}</span>
            </div>
            <div class="flex justify-between font-bold text-lg">
              <span>Total:</span>
              <span class="text-orange-600">Rp{{ formatPrice(cartStore.total) }}</span>
            </div>
          </div>

          <button 
            @click="goToCheckout"
            class="btn-primary w-full"
          >
            Lanjut Checkout
          </button>
          <button 
            @click="close"
            class="btn-secondary w-full"
          >
            Lanjut Belanja
          </button>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
import { computed } from 'vue'
import { useRouter } from 'vue-router'
import { useCartStore } from '@/stores/cart'
import { XMarkIcon, ShoppingCartIcon, TrashIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
  modelValue: Boolean,
})

const emit = defineEmits(['update:modelValue'])
const router = useRouter()
const cartStore = useCartStore()

const isOpen = computed({
  get: () => props.modelValue,
  set: (value) => emit('update:modelValue', value),
})

const formatPrice = (price) => {
  return new Intl.NumberFormat('id-ID').format(price)
}

const close = () => {
  isOpen.value = false
}

const incrementQuantity = async (item) => {
  if (item.quantity < item.product.stock) {
    await cartStore.updateQuantity(item.id, item.quantity + 1)
  }
}

const decrementQuantity = async (item) => {
  if (item.quantity > 1) {
    await cartStore.updateQuantity(item.id, item.quantity - 1)
  } else {
    await removeItem(item.id)
  }
}

const updateQuantity = async (item, quantity) => {
  if (quantity > 0 && quantity <= item.product.stock) {
    await cartStore.updateQuantity(item.id, quantity)
  }
}

const removeItem = async (id) => {
  await cartStore.removeItem(id)
}

const goToCheckout = async () => {
  close()
  await router.push('/checkout')
}
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

.slide-right-enter-active,
.slide-right-leave-active {
  transition: transform 0.3s ease;
}

.slide-right-enter-from,
.slide-right-leave-to {
  transform: translateX(100%);
}
</style>
