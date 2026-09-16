<template>
  <div class="bg-white dark:bg-slate-800 rounded-lg shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
    <div class="sticky top-0 bg-white dark:bg-slate-800 border-b border-slate-200 dark:border-slate-700 p-4 flex justify-between items-center">
      <h2 class="text-2xl font-bold">{{ product.name }}</h2>
      <button @click="$emit('close')" class="text-slate-500 hover:text-slate-700">
        <XMarkIcon class="w-6 h-6" />
      </button>
    </div>

    <div class="p-6 space-y-6">
      <!-- Image Gallery -->
      <div v-if="product.image" class="h-64 bg-slate-200 dark:bg-slate-700 rounded-lg overflow-hidden">
        <img :src="`/storage/${product.image}`" :alt="product.name" class="w-full h-full object-cover" />
      </div>
      <div v-else class="h-64 bg-slate-200 dark:bg-slate-700 rounded-lg flex items-center justify-center text-slate-400">
        <span>No Image</span>
      </div>

      <!-- Product Info -->
      <div>
        <p class="text-slate-600 dark:text-slate-400 mb-2">{{ product.category.name }}</p>
        <h3 class="text-3xl font-bold text-orange-600 mb-4">Rp{{ formatPrice(product.price) }}</h3>
        <p class="text-slate-700 dark:text-slate-300 mb-4">{{ product.description }}</p>

        <div class="flex items-center gap-4 mb-6">
          <span v-if="product.stock > 0" class="badge badge-success">Stok: {{ product.stock }}</span>
          <span v-else class="badge badge-danger">Stok Habis</span>
          <span class="text-yellow-500 font-bold">★ {{ averageRating.toFixed(1) }}/5 ({{ reviewCount }} ulasan)</span>
        </div>
      </div>

      <!-- Quantity Selector -->
      <div v-if="product.stock > 0" class="flex items-center gap-4">
        <label class="font-bold">Jumlah:</label>
        <div class="flex items-center border border-slate-300 dark:border-slate-600 rounded-lg">
          <button @click="quantity > 1 && quantity--" class="px-4 py-2 hover:bg-slate-100 dark:hover:bg-slate-700">−</button>
          <input v-model.number="quantity" type="number" :max="product.stock" class="w-16 text-center border-0 bg-transparent" />
          <button @click="quantity < product.stock && quantity++" class="px-4 py-2 hover:bg-slate-100 dark:hover:bg-slate-700">+</button>
        </div>
      </div>

      <!-- Reviews Section -->
      <div class="border-t border-slate-200 dark:border-slate-700 pt-6">
        <h4 class="font-bold text-lg mb-4">Ulasan Pelanggan</h4>
        <div class="space-y-3 max-h-48 overflow-y-auto mb-4">
          <div v-for="review in reviews" :key="review.id" class="p-3 bg-slate-100 dark:bg-slate-700 rounded">
            <div class="flex justify-between items-center mb-1">
              <span class="font-semibold text-sm">{{ review.user.name }}</span>
              <span class="text-yellow-500 text-sm">★ {{ review.rating }}/5</span>
            </div>
            <p class="text-sm text-slate-600 dark:text-slate-400">{{ review.comment }}</p>
          </div>
        </div>

        <!-- Add Review Form -->
        <form @submit.prevent="submitReview" class="bg-slate-50 dark:bg-slate-700 p-4 rounded-lg">
          <div class="mb-3">
            <label class="block text-sm font-bold mb-1">Rating</label>
            <select v-model.number="newReview.rating" class="input-field">
              <option value="5">5 - Sangat Puas</option>
              <option value="4">4 - Puas</option>
              <option value="3">3 - Cukup</option>
              <option value="2">2 - Kurang</option>
              <option value="1">1 - Kecewa</option>
            </select>
          </div>
          <div class="mb-3">
            <label class="block text-sm font-bold mb-1">Komentar</label>
            <textarea v-model="newReview.comment" class="input-field" rows="3" placeholder="Bagikan pengalaman Anda..."></textarea>
          </div>
          <button type="submit" class="btn-primary text-sm w-full">Kirim Ulasan</button>
        </form>
      </div>

      <!-- Action Buttons -->
      <div class="flex gap-3 border-t border-slate-200 dark:border-slate-700 pt-6">
        <button v-if="product.stock > 0" @click="addToCartHandler" class="flex-1 btn-primary">
          <ShoppingCartIcon class="w-5 h-5 inline mr-2" />
          Tambah ke Keranjang
        </button>
        <button @click="$emit('close')" class="flex-1 btn-secondary">Tutup</button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useCartStore } from '@/stores/cart'
import { XMarkIcon, ShoppingCartIcon } from '@heroicons/vue/24/outline'
import api from '@/services/api'

const props = defineProps({
  product: {
    type: Object,
    required: true,
  },
})

const emit = defineEmits(['close', 'add-to-cart'])
const cartStore = useCartStore()

const quantity = ref(1)
const reviews = ref([])
const newReview = ref({ rating: 5, comment: '' })

const formatPrice = (price) => {
  return new Intl.NumberFormat('id-ID').format(price)
}

const averageRating = computed(() => {
  if (reviews.value.length === 0) return 0
  return reviews.value.reduce((sum, r) => sum + r.rating, 0) / reviews.value.length
})

const reviewCount = computed(() => reviews.value.length)

const fetchReviews = async () => {
  try {
    const response = await api.get(`/api/products/${props.product.id}/reviews`)
    reviews.value = response.data
  } catch (error) {
    console.error('Failed to fetch reviews', error)
  }
}

const submitReview = async () => {
  try {
    await api.post(`/api/products/${props.product.id}/reviews`, newReview.value)
    newReview.value = { rating: 5, comment: '' }
    await fetchReviews()
  } catch (error) {
    console.error('Failed to submit review', error)
  }
}

const addToCartHandler = async () => {
  try {
    await cartStore.addItem(props.product.id, quantity.value)
    emit('add-to-cart', props.product, quantity.value)
    emit('close')
  } catch (error) {
    console.error('Failed to add to cart', error)
  }
}

onMounted(() => {
  fetchReviews()
})
</script>
