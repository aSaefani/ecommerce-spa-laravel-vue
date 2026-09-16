<template>
  <div class="space-y-12">
    <section class="bg-gradient-to-r from-orange-600 to-orange-700 text-white rounded-lg p-12 text-center">
      <h1 class="text-5xl font-bold mb-4">Selamat Datang di Ecommerce</h1>
      <p class="text-xl mb-8">Belanja produk berkualitas dengan sistem modern dan aman</p>
      <RouterLink to="/products" class="inline-block btn-primary text-lg">
        Mulai Belanja
      </RouterLink>
    </section>

    <section class="grid md:grid-cols-3 gap-6">
      <div class="card">
        <div class="text-4xl mb-4">🛍️</div>
        <h3 class="text-xl font-bold mb-2">Produk Berkualitas</h3>
        <p class="text-slate-600 dark:text-slate-400">Pilihan produk terbaik dari berbagai kategori</p>
      </div>
      <div class="card">
        <div class="text-4xl mb-4">💳</div>
        <h3 class="text-xl font-bold mb-2">Pembayaran GoPay</h3>
        <p class="text-slate-600 dark:text-slate-400">Sistem pembayaran aman dengan Midtrans</p>
      </div>
      <div class="card">
        <div class="text-4xl mb-4">⭐</div>
        <h3 class="text-xl font-bold mb-2">Poin Reward</h3>
        <p class="text-slate-600 dark:text-slate-400">Dapatkan poin setiap pembelian</p>
      </div>
    </section>
  </div>
</template>

<script setup>
</script>
