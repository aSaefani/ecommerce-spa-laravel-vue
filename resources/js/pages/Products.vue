<template>
  <div class="grid md:grid-cols-4 gap-6">
    <!-- Sidebar Filter -->
    <div class="md:col-span-1">
      <div class="card glass-heavy space-y-6 sticky top-24 rounded-3xl">
        <!-- Search -->
        <div>
          <label class="block text-sm font-bold mb-2">Cari Produk</label>
          <div class="relative">
            <input 
              v-model="productStore.searchQuery" 
              type="text" 
              placeholder="Nama produk..." 
              class="input-field pl-10"
              @input="debouncedSearch"
            />
            <MagnifyingGlassIcon class="absolute left-3 top-3 w-5 h-5 text-slate-400" />
          </div>
        </div>

        <!-- Category Filter -->
        <div>
          <label class="block text-sm font-bold mb-2">Kategori</label>
          <select 
            v-model="productStore.selectedCategory" 
            class="input-field"
            @change="applyFilters"
          >
            <option :value="null">Semua Kategori</option>
            <option v-for="cat in productStore.categories" :key="cat.id" :value="cat.id">
              {{ cat.name }}
            </option>
          </select>
        </div>

        <!-- Price Range Filter -->
        <div>
          <label class="block text-sm font-bold mb-2">Harga</label>
          <div class="space-y-2 mb-2">
            <div class="flex justify-between text-xs text-slate-600 dark:text-slate-400">
              <span>Rp{{ formatPrice(productStore.priceRange[0]) }}</span>
              <span>Rp{{ formatPrice(productStore.priceRange[1]) }}</span>
            </div>
            <input 
              v-model.number="productStore.priceRange[0]" 
              type="range" 
              min="0" 
              max="1000000" 
              step="50000" 
              class="w-full"
              @change="applyFilters"
            />
            <input 
              v-model.number="productStore.priceRange[1]" 
              type="range" 
              min="0" 
              max="1000000" 
              step="50000" 
              class="w-full"
              @change="applyFilters"
            />
          </div>
        </div>

        <!-- Sort Options -->
        <div>
          <label class="block text-sm font-bold mb-2">Urutkan</label>
          <select v-model="sortBy" class="input-field" @change="applyFilters">
            <option value="newest">Terbaru</option>
            <option value="price_asc">Harga Rendah ke Tinggi</option>
            <option value="price_desc">Harga Tinggi ke Rendah</option>
            <option value="name_asc">Nama A-Z</option>
            <option value="rating">Rating Tinggi</option>
          </select>
        </div>

        <!-- Stock Filter -->
        <div class="flex items-center gap-2">
          <input 
            v-model="inStockOnly" 
            type="checkbox" 
            id="in-stock"
            class="rounded"
            @change="applyFilters"
          />
          <label for="in-stock" class="text-sm font-medium cursor-pointer">Hanya Tersedia</label>
        </div>
      </div>
    </div>

    <!-- Products Grid -->
    <div class="md:col-span-3">
      <!-- Loading Skeletons -->
      <div v-if="loading" class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div v-for="i in 6" :key="i" class="card animate-pulse">
          <div class="h-48 bg-slate-300 dark:bg-slate-600 rounded-lg mb-4"></div>
          <div class="h-4 bg-slate-300 dark:bg-slate-600 rounded mb-2"></div>
          <div class="h-4 bg-slate-300 dark:bg-slate-600 rounded w-3/4"></div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-else-if="filteredProducts.length === 0" class="text-center py-12">
        <div class="text-6xl mb-4">🔍</div>
        <p class="text-slate-600 dark:text-slate-400 text-lg">Tidak ada produk yang sesuai</p>
        <button @click="resetFilters" class="btn-primary mt-4">Reset Filter</button>
      </div>

      <!-- Products Grid with Animation -->
      <div v-else class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div 
          v-for="product in filteredProducts" 
          :key="product.id"
          class="card hover:shadow-2xl hover:bg-white/55 dark:hover:bg-slate-800/55 transition-all duration-300 cursor-pointer group transform hover:-translate-y-2 border border-white/40 dark:border-slate-700/50"
          @click="openProductDetail(product)"
        >
          <!-- Image with Badge -->
          <div class="relative h-48 bg-slate-200 dark:bg-slate-700 rounded-lg mb-4 overflow-hidden">
            <img 
              v-if="product.image" 
              :src="`/storage/${product.image}`" 
              :alt="product.name" 
              class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300"
            />
            <div v-else class="w-full h-full flex items-center justify-center text-slate-400">
              <span>No Image</span>
            </div>

            <!-- Stock Badge -->
            <span 
              v-if="product.stock > 0" 
              class="absolute top-2 right-2 badge badge-success text-xs animate-bounce"
            >
              Stok: {{ product.stock }}
            </span>
            <span v-else class="absolute top-2 right-2 badge badge-danger text-xs">Habis</span>

            <!-- Rating Badge -->
            <div v-if="product.reviews_avg_rating" class="absolute top-2 left-2 badge badge-secondary text-xs">
              ★ {{ product.reviews_avg_rating.toFixed(1) }}
            </div>
          </div>

          <!-- Product Info -->
          <h3 class="font-bold text-lg truncate mb-1 group-hover:text-orange-600 transition-colors">
            {{ product.name }}
          </h3>
          <p class="text-sm text-slate-600 dark:text-slate-400 mb-2">{{ product.category.name }}</p>
          <p class="text-sm text-slate-700 dark:text-slate-300 line-clamp-2 mb-4">{{ product.description }}</p>

          <!-- Price & Wishlist -->
          <div class="flex justify-between items-center mb-4">
            <span class="text-xl font-bold text-orange-600">Rp{{ formatPrice(product.price) }}</span>
            <button 
              @click.stop="toggleWishlist(product.id)" 
              class="text-red-500 hover:text-red-600 hover:scale-125 transition-transform"
            >
              <HeartIcon class="w-5 h-5" :class="{ 'fill-current': isInWishlist(product.id) }" />
            </button>
          </div>

          <!-- Action Buttons -->
          <div class="flex gap-2">
            <button 
              v-if="product.stock > 0" 
              @click.stop="addToCartQuick(product)"
              class="flex-1 btn-primary text-sm"
            >
              <ShoppingCartIcon class="w-4 h-4 inline mr-1" /> Keranjang
            </button>
            <button v-else class="flex-1 btn-secondary text-sm opacity-50 cursor-not-allowed">Habis</button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Product Detail Modal -->
  <Teleport to="body">
    <div v-if="selectedProduct" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
      <ProductDetailModal :product="selectedProduct" @close="selectedProduct = null" @add-to-cart="addToCart" />
    </div>
  </Teleport>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useProductStore } from '@/stores/products'
import { useCartStore } from '@/stores/cart'
import { HeartIcon, ShoppingCartIcon, MagnifyingGlassIcon } from '@heroicons/vue/24/outline'
import ProductDetailModal from '@/components/ProductDetailModal.vue'
import api from '@/services/api'

const productStore = useProductStore()
const cartStore = useCartStore()

const selectedProduct = ref(null)
const wishlist = ref([])
const loading = ref(false)
const sortBy = ref('newest')
const inStockOnly = ref(false)
const allProducts = ref([])
let searchTimeout = null

const filteredProducts = computed(() => {
  let filtered = allProducts.value

  if (productStore.selectedCategory) {
    filtered = filtered.filter(p => p.category_id === productStore.selectedCategory)
  }

  if (productStore.searchQuery) {
    const query = productStore.searchQuery.toLowerCase()
    filtered = filtered.filter(p => 
      p.name.toLowerCase().includes(query) || 
      p.description?.toLowerCase().includes(query)
    )
  }

  if (productStore.priceRange[0]) {
    filtered = filtered.filter(p => p.price >= productStore.priceRange[0])
  }

  if (productStore.priceRange[1]) {
    filtered = filtered.filter(p => p.price <= productStore.priceRange[1])
  }

  if (inStockOnly.value) {
    filtered = filtered.filter(p => p.stock > 0)
  }

  return filtered
})

const formatPrice = (price) => {
  return new Intl.NumberFormat('id-ID').format(price)
}

const debouncedSearch = () => {
  clearTimeout(searchTimeout)
  searchTimeout = setTimeout(() => {
    applyFilters()
  }, 300)
}

const applyFilters = async () => {
  try {
    loading.value = true
    const response = await api.get('/api/products', {
      params: {
        search: productStore.searchQuery,
        category_id: productStore.selectedCategory,
        min_price: productStore.priceRange[0],
        max_price: productStore.priceRange[1],
        in_stock: inStockOnly.value,
        sort: sortBy.value,
        all: true,
      }
    })
    allProducts.value = response.data
  } catch (error) {
    console.error('Failed to fetch products', error)
  } finally {
    loading.value = false
  }
}

const resetFilters = () => {
  productStore.searchQuery = ''
  productStore.selectedCategory = null
  productStore.priceRange = [0, 1000000]
  sortBy.value = 'newest'
  inStockOnly.value = false
  applyFilters()
}

const openProductDetail = (product) => {
  selectedProduct.value = product
}

const toggleWishlist = async (productId) => {
  try {
    const product = allProducts.value.find(p => p.id === productId)
    if (!product) return

    const response = await api.post(`/api/products/${productId}/wishlist`)
    
    if (response.data.status === 'added') {
      wishlist.value.push(productId)
    } else {
      wishlist.value = wishlist.value.filter(id => id !== productId)
    }
  } catch (error) {
    console.error('Failed to toggle wishlist', error)
  }
}

const isInWishlist = (productId) => {
  return wishlist.value.includes(productId)
}

const addToCartQuick = async (product) => {
  try {
    await cartStore.addItem(product.id, 1)
  } catch (error) {
    console.error('Failed to add to cart', error)
  }
}

onMounted(async () => {
  await productStore.fetchCategories()
  await applyFilters()
})
</script>
