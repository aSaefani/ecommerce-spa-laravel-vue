import { createRouter, createWebHistory } from 'vue-router'
import Home from '@/pages/Home.vue'
import Products from '@/pages/Products.vue'
import Orders from '@/pages/Orders.vue'
import Points from '@/pages/Points.vue'
import Wishlist from '@/pages/Wishlist.vue'
import CashierDashboard from '@/pages/CashierDashboard.vue'
import AdminDashboard from '@/pages/AdminDashboard.vue'
import CustomerDashboard from '@/pages/CustomerDashboard.vue'
import Checkout from '@/pages/Checkout.vue'

import Login from '@/pages/Login.vue'
import Register from '@/pages/Register.vue'

const routes = [
  { path: '/', component: Home, name: 'home' },
  { path: '/login', component: Login, name: 'login', meta: { guest: true } },
  { path: '/register', component: Register, name: 'register', meta: { guest: true } },
  { path: '/products', component: Products, name: 'products' },
  { path: '/dashboard', component: CustomerDashboard, name: 'dashboard', meta: { requiresAuth: true } },
  { path: '/checkout', component: Checkout, name: 'checkout', meta: { requiresAuth: true } },
  { path: '/orders', component: Orders, name: 'orders', meta: { requiresAuth: true } },
  { path: '/points', component: Points, name: 'points' },
  { path: '/wishlist', component: Wishlist, name: 'wishlist' },
  { path: '/cashier', component: CashierDashboard, name: 'cashier', meta: { requiresRole: 'cashier' } },
  { path: '/admin', component: AdminDashboard, name: 'admin', meta: { requiresRole: 'admin' } },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

export default router
