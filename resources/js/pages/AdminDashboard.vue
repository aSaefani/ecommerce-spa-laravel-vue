<template>
  <div class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
      <div>
        <h1 class="text-3xl font-bold">Admin Control Center</h1>
        <p class="text-slate-500 text-sm">Kelola metrik, produk, stok, kategori, dan pengguna.</p>
      </div>

      <!-- Navigation Tabs -->
      <div class="flex flex-wrap gap-2">
        <button 
          v-for="tab in tabs" 
          :key="tab.id"
          @click="activeTab = tab.id"
          :class="[
            'px-4 py-2 rounded-lg text-sm font-medium transition-all',
            activeTab === tab.id ? 'bg-orange-600 text-white shadow-md' : 'bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-100'
          ]"
        >
          {{ tab.name }}
        </button>
      </div>
    </div>

    <!-- TAB 1: OVERVIEW & STATS -->
    <div v-if="activeTab === 'overview'" class="space-y-6">
      <div v-if="loadingStats" class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div v-for="i in 4" :key="i" class="card h-28 animate-pulse bg-slate-200 dark:bg-slate-700"></div>
      </div>
      <div v-else class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="card bg-gradient-to-br from-orange-500 to-orange-600 text-white">
          <p class="text-xs uppercase tracking-wider text-orange-100 font-semibold">Total Pendapatan</p>
          <p class="text-2xl font-bold mt-2">Rp{{ formatPrice(stats.totalRevenue) }}</p>
          <p class="text-xs text-orange-100 mt-2">Hari ini: Rp{{ formatPrice(stats.dailyRevenue) }}</p>
        </div>
        <div class="card">
          <p class="text-xs uppercase tracking-wider text-slate-500 font-semibold">Total Pesanan</p>
          <p class="text-2xl font-bold mt-2 text-slate-800 dark:text-slate-100">{{ stats.totalOrders }}</p>
          <p class="text-xs text-emerald-600 mt-2 font-medium">Status terverifikasi</p>
        </div>
        <div class="card">
          <p class="text-xs uppercase tracking-wider text-slate-500 font-semibold">Total Produk</p>
          <p class="text-2xl font-bold mt-2 text-slate-800 dark:text-slate-100">{{ stats.totalProducts }}</p>
          <p class="text-xs text-slate-500 mt-2">Katalog aktif</p>
        </div>
        <div class="card">
          <p class="text-xs uppercase tracking-wider text-slate-500 font-semibold">Pelanggan Terdaftar</p>
          <p class="text-2xl font-bold mt-2 text-slate-800 dark:text-slate-100">{{ stats.totalCustomers }}</p>
          <p class="text-xs text-blue-600 mt-2 font-medium">Member aktif</p>
        </div>
      </div>

      <!-- Recent Orders Table -->
      <div class="card">
        <div class="flex justify-between items-center mb-4">
          <h2 class="text-lg font-bold">10 Transaksi Terbaru</h2>
          <a href="/admin/reports/daily/pdf" target="_blank" class="btn-secondary text-xs">Download Laporan PDF</a>
        </div>
        <div class="overflow-x-auto">
          <table class="w-full text-left text-sm">
            <thead class="bg-slate-50 dark:bg-slate-700/50 text-slate-600 dark:text-slate-300">
              <tr>
                <th class="p-3">Order #</th>
                <th class="p-3">Pelanggan</th>
                <th class="p-3">Total</th>
                <th class="p-3">Status</th>
                <th class="p-3">Tanggal</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
              <tr v-for="order in stats.recentOrders" :key="order.id">
                <td class="p-3 font-mono font-medium">{{ order.order_number }}</td>
                <td class="p-3">{{ order.user?.name || 'Guest' }}</td>
                <td class="p-3 font-bold text-orange-600">Rp{{ formatPrice(order.total) }}</td>
                <td class="p-3">
                  <span :class="statusBadge(order.status)">{{ order.status }}</span>
                </td>
                <td class="p-3 text-slate-500">{{ formatDate(order.created_at) }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- TAB 2: PRODUK CRUD -->
    <div v-if="activeTab === 'products'" class="space-y-4">
      <div class="flex justify-between items-center">
        <h2 class="text-xl font-bold">Manajemen Produk</h2>
        <button @click="openProductModal()" class="btn-primary text-sm">+ Tambah Produk</button>
      </div>

      <div class="card overflow-x-auto">
        <table class="w-full text-left text-sm">
          <thead class="bg-slate-50 dark:bg-slate-700/50">
            <tr>
              <th class="p-3">Nama</th>
              <th class="p-3">Kategori</th>
              <th class="p-3">Harga</th>
              <th class="p-3">Stok</th>
              <th class="p-3 text-right">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
            <tr v-for="prod in products" :key="prod.id">
              <td class="p-3 font-medium">{{ prod.name }}</td>
              <td class="p-3 text-slate-500">{{ prod.category?.name }}</td>
              <td class="p-3 font-semibold text-orange-600">Rp{{ formatPrice(prod.price) }}</td>
              <td class="p-3">
                <span :class="prod.stock > 0 ? 'text-emerald-600 font-bold' : 'text-red-500 font-bold'">
                  {{ prod.stock }}
                </span>
              </td>
              <td class="p-3 text-right space-x-2">
                <button @click="openProductModal(prod)" class="text-blue-600 hover:underline text-xs font-semibold">Edit</button>
                <button @click="deleteProduct(prod.id)" class="text-red-600 hover:underline text-xs font-semibold">Hapus</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- TAB 3: KATEGORI CRUD -->
    <div v-if="activeTab === 'categories'" class="space-y-4">
      <div class="flex justify-between items-center">
        <h2 class="text-xl font-bold">Kategori Produk</h2>
        <button @click="openCatModal()" class="btn-primary text-sm">+ Tambah Kategori</button>
      </div>

      <div class="grid md:grid-cols-3 gap-4">
        <div v-for="cat in categories" :key="cat.id" class="card flex justify-between items-start">
          <div>
            <h3 class="font-bold text-base">{{ cat.name }}</h3>
            <p class="text-xs text-slate-500 mt-1">{{ cat.description || 'Tanpa deskripsi' }}</p>
            <p class="text-xs font-semibold text-orange-600 mt-2">{{ cat.products_count || 0 }} Produk</p>
          </div>
          <div class="space-x-2">
            <button @click="openCatModal(cat)" class="text-blue-600 text-xs font-semibold">Edit</button>
            <button @click="deleteCat(cat.id)" class="text-red-600 text-xs font-semibold">Hapus</button>
          </div>
        </div>
      </div>
    </div>

    <!-- TAB 4: USERS MANAGEMENT -->
    <div v-if="activeTab === 'users'" class="space-y-4">
      <h2 class="text-xl font-bold">Manajemen Pengguna</h2>
      <div class="card overflow-x-auto">
        <table class="w-full text-left text-sm">
          <thead class="bg-slate-50 dark:bg-slate-700/50">
            <tr>
              <th class="p-3">Nama</th>
              <th class="p-3">Email</th>
              <th class="p-3">Role</th>
              <th class="p-3">Status</th>
              <th class="p-3 text-right">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
            <tr v-for="u in users" :key="u.id">
              <td class="p-3 font-medium">{{ u.name }}</td>
              <td class="p-3 text-slate-500">{{ u.email }}</td>
              <td class="p-3"><span class="badge badge-secondary text-xs uppercase">{{ u.role }}</span></td>
              <td class="p-3">
                <span :class="u.is_active ? 'text-emerald-600 font-bold' : 'text-red-500 font-bold'">
                  {{ u.is_active ? 'Aktif' : 'Nonaktif' }}
                </span>
              </td>
              <td class="p-3 text-right">
                <button 
                  @click="toggleUser(u)" 
                  :class="u.is_active ? 'text-amber-600 hover:underline text-xs' : 'text-emerald-600 hover:underline text-xs'"
                >
                  {{ u.is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- TAB 5: INVENTORY AUDIT LOGS -->
    <div v-if="activeTab === 'inventory'" class="space-y-4">
      <h2 class="text-xl font-bold">Audit Trail Stok (Inventory Logs)</h2>
      <div class="card overflow-x-auto">
        <table class="w-full text-left text-sm">
          <thead class="bg-slate-50 dark:bg-slate-700/50">
            <tr>
              <th class="p-3">Tanggal</th>
              <th class="p-3">Produk</th>
              <th class="p-3">Tipe</th>
              <th class="p-3">Qty</th>
              <th class="p-3">Keterangan</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
            <tr v-for="log in inventoryLogs" :key="log.id">
              <td class="p-3 text-slate-500 text-xs">{{ formatDate(log.created_at) }}</td>
              <td class="p-3 font-medium">{{ log.product?.name }}</td>
              <td class="p-3">
                <span :class="log.type === 'in' ? 'badge badge-success text-xs' : 'badge badge-danger text-xs'">
                  {{ log.type === 'in' ? 'MASUK' : 'KELUAR' }}
                </span>
              </td>
              <td class="p-3 font-bold">{{ log.quantity }}</td>
              <td class="p-3 text-slate-500 text-xs">{{ log.description }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- MODAL PRODUCT -->
    <Teleport to="body">
      <div v-if="showProductModal" class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
        <div class="card max-w-lg w-full p-6 space-y-4">
          <h3 class="font-bold text-lg">{{ editingProduct ? 'Edit Produk' : 'Tambah Produk Baru' }}</h3>
          <div>
            <label class="block text-xs font-semibold mb-1">Nama Produk</label>
            <input v-model="productForm.name" type="text" class="input-field text-sm" required />
          </div>
          <div>
            <label class="block text-xs font-semibold mb-1">Kategori</label>
            <select v-model="productForm.category_id" class="input-field text-sm">
              <option v-for="c in categories" :key="c.id" :value="c.id">{{ c.name }}</option>
            </select>
          </div>
          <div class="grid grid-cols-2 gap-2">
            <div>
              <label class="block text-xs font-semibold mb-1">Harga (Rp)</label>
              <input v-model.number="productForm.price" type="number" class="input-field text-sm" required />
            </div>
            <div>
              <label class="block text-xs font-semibold mb-1">Stok</label>
              <input v-model.number="productForm.stock" type="number" class="input-field text-sm" required />
            </div>
          </div>
          <div>
            <label class="block text-xs font-semibold mb-1">Deskripsi</label>
            <textarea v-model="productForm.description" class="input-field text-sm" rows="3"></textarea>
          </div>
          <div class="flex justify-end gap-2 pt-2">
            <button @click="showProductModal = false" class="btn-secondary text-sm">Batal</button>
            <button @click="saveProduct" class="btn-primary text-sm">Simpan</button>
          </div>
        </div>
      </div>
    </Teleport>

    <!-- MODAL CATEGORY -->
    <Teleport to="body">
      <div v-if="showCatModal" class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
        <div class="card max-w-md w-full p-6 space-y-4">
          <h3 class="font-bold text-lg">{{ editingCat ? 'Edit Kategori' : 'Tambah Kategori' }}</h3>
          <div>
            <label class="block text-xs font-semibold mb-1">Nama Kategori</label>
            <input v-model="catForm.name" type="text" class="input-field text-sm" required />
          </div>
          <div>
            <label class="block text-xs font-semibold mb-1">Deskripsi</label>
            <textarea v-model="catForm.description" class="input-field text-sm" rows="2"></textarea>
          </div>
          <div class="flex justify-end gap-2 pt-2">
            <button @click="showCatModal = false" class="btn-secondary text-sm">Batal</button>
            <button @click="saveCat" class="btn-primary text-sm">Simpan</button>
          </div>
        </div>
      </div>
    </Teleport>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import api from '@/services/api'

const activeTab = ref('overview')
const tabs = [
  { id: 'overview', name: 'Ringkasan & Metrik' },
  { id: 'products', name: 'Produk' },
  { id: 'categories', name: 'Kategori' },
  { id: 'users', name: 'Pengguna' },
  { id: 'inventory', name: 'Log Stok' },
]

const loadingStats = ref(true)
const stats = reactive({ totalOrders: 0, totalRevenue: 0, totalCustomers: 0, totalProducts: 0, dailyRevenue: 0, recentOrders: [] })
const products = ref([])
const categories = ref([])
const users = ref([])
const inventoryLogs = ref([])

// Product Modal State
const showProductModal = ref(false)
const editingProduct = ref(null)
const productForm = reactive({ category_id: null, name: '', description: '', price: 0, stock: 0 })

// Category Modal State
const showCatModal = ref(false)
const editingCat = ref(null)
const catForm = reactive({ name: '', description: '' })

const formatPrice = (val) => new Intl.NumberFormat('id-ID').format(val || 0)
const formatDate = (val) => new Date(val).toLocaleDateString('id-ID', { dateStyle: 'short', timeStyle: 'short' })
const statusBadge = (s) => ({
  paid: 'badge badge-success text-xs',
  pending: 'badge badge-warning text-xs',
  completed: 'badge badge-success text-xs',
  cancelled: 'badge badge-danger text-xs'
}[s] || 'badge badge-secondary text-xs')

const fetchStats = async () => {
  try {
    loadingStats.value = true
    const res = await api.get('/api/admin/dashboard')
    Object.assign(stats, res.data)
  } finally {
    loadingStats.value = false
  }
}

const fetchProducts = async () => {
  const res = await api.get('/api/products?all=true')
  products.value = res.data
}

const fetchCategories = async () => {
  const res = await api.get('/api/categories')
  categories.value = res.data
}

const fetchUsers = async () => {
  const res = await api.get('/api/admin/users')
  users.value = res.data
}

const fetchLogs = async () => {
  const res = await api.get('/api/admin/inventory/logs')
  inventoryLogs.value = res.data
}

const openProductModal = (prod = null) => {
  editingProduct.value = prod
  if (prod) {
    Object.assign(productForm, { category_id: prod.category_id, name: prod.name, description: prod.description, price: prod.price, stock: prod.stock })
  } else {
    Object.assign(productForm, { category_id: categories.value[0]?.id || null, name: '', description: '', price: 0, stock: 0 })
  }
  showProductModal.value = true
}

const saveProduct = async () => {
  if (editingProduct.value) {
    await api.put(`/api/products/${editingProduct.value.id}`, productForm)
  } else {
    await api.post('/api/products', productForm)
  }
  showProductModal.value = false
  await fetchProducts()
  await fetchStats()
}

const deleteProduct = async (id) => {
  if (confirm('Yakin ingin menghapus produk ini?')) {
    await api.delete(`/api/products/${id}`)
    await fetchProducts()
    await fetchStats()
  }
}

const openCatModal = (cat = null) => {
  editingCat.value = cat
  if (cat) {
    Object.assign(catForm, { name: cat.name, description: cat.description })
  } else {
    Object.assign(catForm, { name: '', description: '' })
  }
  showCatModal.value = true
}

const saveCat = async () => {
  if (editingCat.value) {
    await api.put(`/api/categories/${editingCat.value.id}`, catForm)
  } else {
    await api.post('/api/categories', catForm)
  }
  showCatModal.value = false
  await fetchCategories()
}

const deleteCat = async (id) => {
  if (confirm('Yakin ingin menghapus kategori? Semua produk terkait ikut terhapus.')) {
    await api.delete(`/api/categories/${id}`)
    await fetchCategories()
  }
}

const toggleUser = async (u) => {
  await api.post(`/api/admin/users/${u.id}/toggle`)
  await fetchUsers()
}

onMounted(async () => {
  await fetchStats()
  await fetchProducts()
  await fetchCategories()
  await fetchUsers()
  await fetchLogs()
})
</script>
