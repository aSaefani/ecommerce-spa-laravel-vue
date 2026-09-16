<template>
  <div class="space-y-12">
    <section class="glass-heavy rounded-3xl p-12 md:p-20 text-center relative overflow-hidden">
      <div class="absolute -top-20 -left-20 w-64 h-64 bg-fuchsia-500 rounded-full filter blur-3xl opacity-30"></div>
      <div class="absolute -bottom-20 -right-20 w-64 h-64 bg-indigo-500 rounded-full filter blur-3xl opacity-30"></div>
      <div class="relative z-10">
        <h1 class="text-4xl md:text-6xl font-black mb-4 bg-clip-text text-transparent bg-gradient-to-r from-fuchsia-600 via-indigo-600 to-teal-600 dark:from-fuchsia-400 dark:via-indigo-400 dark:to-teal-400">Selamat Datang di Ecommerce</h1>
        <p class="text-lg md:text-xl mb-8 text-slate-600 dark:text-slate-300">Belanja produk berkualitas dengan sistem modern dan aman</p>
        <RouterLink to="/products" class="inline-block btn-primary text-lg !px-8 !py-3">
          Mulai Belanja
        </RouterLink>
      </div>
    </section>

    <section class="grid md:grid-cols-3 gap-6">
      <div class="card hover:-translate-y-2 hover:shadow-2xl">
        <div class="text-4xl mb-4">🛍️</div>
        <h3 class="text-xl font-bold mb-2">Produk Berkualitas</h3>
        <p class="text-slate-600 dark:text-slate-400">Pilihan produk terbaik dari berbagai kategori</p>
      </div>
      <div class="card hover:-translate-y-2 hover:shadow-2xl">
        <div class="text-4xl mb-4">💳</div>
        <h3 class="text-xl font-bold mb-2">Pembayaran GoPay</h3>
        <p class="text-slate-600 dark:text-slate-400">Sistem pembayaran aman dengan Midtrans</p>
      </div>
      <div class="card hover:-translate-y-2 hover:shadow-2xl">
        <div class="text-4xl mb-4">⭐</div>
        <h3 class="text-xl font-bold mb-2">Poin Reward</h3>
        <p class="text-slate-600 dark:text-slate-400">Dapatkan poin setiap pembelian</p>
      </div>
    </section>
  </div>
</template>

<script setup>
</script>
