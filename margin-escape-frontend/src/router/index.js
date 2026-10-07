import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import LoginView from '../views/LoginView.vue'
import AppLayout from '../layouts/AppLayout.vue'
import InputStokView from '../views/staff/InputStokView.vue'
import DashboardOwnerView from '../views/DashboardOwnerView.vue'
import RiwayatStokView from '../views/staff/RiwayatStokView.vue'
import NotifikasiView from '../views/staff/NotifikasiView.vue'
import ForecastingView from '../views/owner/ForecastingView.vue'
import LaporanView from '../views/owner/LaporanView.vue'
import KelolaStaffView from '../views/owner/KelolaStaffView.vue'

const routes = [
  { path: '/', redirect: '/login' },
  { path: '/login', component: LoginView },
  {
    path: '/',
    component: AppLayout,
    meta: { requiresAuth: true },
    children: [
      { path: 'dashboard', component: DashboardOwnerView },
      { path: 'input-stok', component: InputStokView },
      { path: 'riwayat', component: RiwayatStokView },
      { path: 'notifikasi', component: NotifikasiView },
      { path: 'forecasting', component: ForecastingView },
      { path: 'laporan', component: LaporanView },
      { path: 'kelola-staff', component: KelolaStaffView },
    ],
  },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

router.beforeEach((to, from, next) => {
  const authStore = useAuthStore()
  if (to.meta.requiresAuth && !authStore.isLoggedIn) {
    next('/login')
  } else {
    next()
  }
})

export default router