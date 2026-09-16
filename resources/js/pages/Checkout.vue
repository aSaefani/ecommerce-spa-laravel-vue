<template>
  <div class="max-w-4xl mx-auto glass-heavy rounded-3xl p-5 md:p-8">
    <!-- Progress Stepper -->
    <div class="mb-8 flex justify-between">
      <div v-for="(step, idx) in steps" :key="idx" class="flex-1 flex items-center">
        <div 
          :class="[
            'w-10 h-10 rounded-full flex items-center justify-center font-bold',
            currentStep > idx ? 'bg-green-500 text-white' : 
            currentStep === idx ? 'bg-orange-600 text-white' : 
            'bg-slate-200 dark:bg-slate-700 text-slate-600 dark:text-slate-400'
          ]"
        >
          {{ idx + 1 }}
        </div>
        <div v-if="idx < steps.length - 1" class="flex-1 h-1 mx-2" :class="currentStep > idx ? 'bg-green-500' : 'bg-slate-300 dark:bg-slate-600'"></div>
      </div>
    </div>

    <!-- Step Labels -->
    <div class="mb-8 flex justify-between text-sm text-slate-600 dark:text-slate-400">
      <span v-for="step in steps" :key="step">{{ step }}</span>
    </div>

    <div class="grid md:grid-cols-3 gap-6">
      <!-- Main Content -->
      <div class="md:col-span-2">
        <!-- Step 1: Review Items -->
        <div v-show="currentStep === 0" class="space-y-4">
          <h2 class="text-2xl font-bold mb-4">Review Pesanan</h2>
          
          <div v-for="item in cartStore.items" :key="item.id" class="card p-4 flex gap-4">
            <img 
              v-if="item.product.image"
              :src="`/storage/${item.product.image}`"
              :alt="item.product.name"
              class="w-20 h-20 object-cover rounded"
            />
            <div class="flex-1">
              <h3 class="font-bold">{{ item.product.name }}</h3>
              <p class="text-sm text-slate-600 dark:text-slate-400">{{ item.product.category.name }}</p>
              <p class="text-orange-600 font-bold mt-2">{{ item.quantity }}x Rp{{ formatPrice(item.product.price) }}</p>
            </div>
            <div class="text-right font-bold">
              Rp{{ formatPrice(item.product.price * item.quantity) }}
            </div>
          </div>

          <button @click="nextStep" class="btn-primary w-full mt-6">Lanjut ke Diskon</button>
        </div>

        <!-- Step 2: Discount Code -->
        <div v-show="currentStep === 1" class="space-y-4">
          <h2 class="text-2xl font-bold mb-4">Masukkan Kode Diskon</h2>
          
          <div class="card p-6">
            <label class="block text-sm font-bold mb-2">Kode Diskon (Opsional)</label>
            <div class="flex gap-2">
              <input 
                v-model="discountCode"
                type="text"
                placeholder="HEMAT10, POTONG20RB"
                class="input-field flex-1"
              />
              <button @click="applyDiscount" class="btn-primary">Terapkan</button>
            </div>

            <div v-if="discountApplied" class="mt-4 p-4 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200 dark:border-green-800">
              <p class="text-green-700 dark:text-green-300 font-bold">✓ Diskon berhasil diterapkan!</p>
              <p class="text-sm text-green-600 dark:text-green-400 mt-1">Hemat Rp{{ formatPrice(discountAmount) }}</p>
            </div>

            <div v-if="discountError" class="mt-4 p-4 bg-red-50 dark:bg-red-900/20 rounded-lg border border-red-200 dark:border-red-800">
              <p class="text-red-700 dark:text-red-300 font-bold">✗ {{ discountError }}</p>
            </div>
          </div>

          <div class="flex gap-4">
            <button @click="prevStep" class="btn-secondary flex-1">Kembali</button>
            <button @click="nextStep" class="btn-primary flex-1">Lanjut Pembayaran</button>
          </div>
        </div>

        <!-- Step 3: Payment -->
        <div v-show="currentStep === 2" class="space-y-4">
          <h2 class="text-2xl font-bold mb-4">Konfirmasi Pesanan</h2>
          
          <div class="card p-6 bg-slate-50 dark:bg-slate-700/50">
            <div class="space-y-3">
              <div class="flex justify-between">
                <span>Subtotal:</span>
                <span>Rp{{ formatPrice(subtotal) }}</span>
              </div>
              <div v-if="discountApplied" class="flex justify-between text-red-600">
                <span>Diskon:</span>
                <span>-Rp{{ formatPrice(discountAmount) }}</span>
              </div>
              <div class="border-t border-slate-300 dark:border-slate-600 pt-3 flex justify-between font-bold text-lg">
                <span>Total Pembayaran:</span>
                <span class="text-orange-600">Rp{{ formatPrice(total) }}</span>
              </div>
            </div>
          </div>

          <div class="card p-6">
            <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">Metode Pembayaran: GoPay</p>
            <button 
              @click="checkout"
              :disabled="processing"
              class="btn-primary w-full disabled:opacity-50"
            >
              <span v-if="!processing">Bayar dengan GoPay</span>
              <span v-else>Memproses...</span>
            </button>
          </div>

          <button @click="prevStep" class="btn-secondary w-full">Kembali</button>
        </div>
      </div>

      <!-- Order Summary Sidebar -->
      <div class="md:col-span-1">
        <div class="card sticky top-20 space-y-4">
          <h3 class="font-bold text-lg">Ringkasan Pesanan</h3>
          
          <div class="space-y-2 text-sm border-b border-slate-200 dark:border-slate-700 pb-4">
            <div class="flex justify-between">
              <span>{{ cartStore.items.length }} Produk</span>
              <span>Rp{{ formatPrice(subtotal) }}</span>
            </div>
            <div v-if="discountApplied" class="flex justify-between text-green-600">
              <span>Diskon</span>
              <span>-Rp{{ formatPrice(discountAmount) }}</span>
            </div>
          </div>

          <div class="flex justify-between font-bold text-lg">
            <span>Total</span>
            <span class="text-orange-600">Rp{{ formatPrice(total) }}</span>
          </div>

          <div class="bg-blue-50 dark:bg-blue-900/20 p-3 rounded-lg border border-blue-200 dark:border-blue-800">
            <p class="text-sm text-blue-700 dark:text-blue-300 font-bold">💰 Poin yang Didapat</p>
            <p class="text-2xl font-bold text-blue-600 dark:text-blue-400 mt-1">{{ estimatedPoints }}</p>
          </div>

          <div class="bg-yellow-50 dark:bg-yellow-900/20 p-3 rounded-lg border border-yellow-200 dark:border-yellow-800">
            <p class="text-xs text-yellow-700 dark:text-yellow-300">Setiap pembelian menghasilkan 1 poin per Rp100</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useCartStore } from '@/stores/cart'
import api from '@/services/api'

const router = useRouter()
const cartStore = useCartStore()

const steps = ['Review', 'Diskon', 'Pembayaran']
const currentStep = ref(0)
const discountCode = ref('')
const discountApplied = ref(false)
const discountAmount = ref(0)
const discountError = ref('')
const processing = ref(false)

const subtotal = computed(() => cartStore.total)
const total = computed(() => subtotal.value - discountAmount.value)
const estimatedPoints = computed(() => Math.floor(total.value / 100))

const formatPrice = (price) => {
  return new Intl.NumberFormat('id-ID').format(price)
}

const nextStep = () => {
  if (currentStep.value < steps.length - 1) {
    currentStep.value++
  }
}

const prevStep = () => {
  if (currentStep.value > 0) {
    currentStep.value--
  }
}

const applyDiscount = async () => {
  if (!discountCode.value) {
    discountError.value = 'Masukkan kode diskon'
    return
  }

  try {
    discountError.value = ''
    const response = await api.post('/api/checkout/validate-discount', {
      code: discountCode.value,
      subtotal: subtotal.value,
    })

    discountAmount.value = response.data.discount_amount
    discountApplied.value = true
  } catch (error) {
    discountError.value = error.response?.data?.message || 'Kode diskon tidak valid'
    discountApplied.value = false
    discountAmount.value = 0
  }
}

const checkout = async () => {
  try {
    processing.value = true

    const response = await api.post('/api/checkout', {
      discount_code: discountCode.value || null,
    })

    const order = response.data.order
    
    if (response.data.snap_token) {
      window.snap.pay(response.data.snap_token, {
        onSuccess: async () => {
          await router.push(`/orders/${order.id}`)
        },
        onPending: async () => {
          await router.push(`/orders/${order.id}`)
        },
        onError: () => {
          processing.value = false
        },
      })
    } else {
      await router.push(`/orders/${order.id}`)
    }
  } catch (error) {
    console.error('Checkout failed', error)
    processing.value = false
  }
}

onMounted(() => {
  if (cartStore.items.length === 0) {
    router.push('/products')
  }
  cartStore.fetchCart()
})
</script>
