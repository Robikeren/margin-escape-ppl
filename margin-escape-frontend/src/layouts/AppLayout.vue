<script setup>
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import logo from '../assets/logo.png'

const router = useRouter()
const authStore = useAuthStore()

function handleLogout() {
  authStore.logout().then(() => router.push('/login'))
}
</script>

<template>
  <div class="app-layout">
    <aside class="sidebar">
      <img :src="logo" alt="Margin Escape" class="logo" />

      <nav>
        <template v-if="authStore.isOwner">
          <router-link to="/dashboard">Dashboard</router-link>
          <router-link to="/forecasting">Forecasting</router-link>
          <router-link to="/riwayat">Riwayat Stok</router-link>
          <router-link to="/notifikasi">Notifikasi</router-link>
          <router-link to="/laporan">Laporan</router-link>
          <router-link to="/kelola-staff">Kelola Staff</router-link>
        </template>
        <template v-else>
          <router-link to="/input-stok">Input Stok</router-link>
          <router-link to="/riwayat">Riwayat Stok</router-link>
          <router-link to="/notifikasi">Notifikasi</router-link>
        </template>
      </nav>

      <div class="user-info">
        <span>{{ authStore.user?.nama }}</span>
        <button @click="handleLogout">Keluar</button>
      </div>
    </aside>

    <main class="content">
      <div class="content-inner">
        <router-view />
      </div>
    </main>
  </div>
</template>

<style scoped>
.app-layout {
  display: flex;
  min-height: 100vh;
}

.sidebar {
  width: 220px;
  flex-shrink: 0;
  background: var(--color-primary);
  color: white;
  display: flex;
  flex-direction: column;
  padding: 1.5rem 1rem;
}

.logo {
  height: 40px;
  margin-bottom: 2rem;
  align-self: flex-start;
}

nav {
  display: flex;
  flex-direction: column;
  gap: 0.25rem;
  flex: 1;
}

nav a {
  color: rgba(255, 255, 255, 0.85);
  text-decoration: none;
  font-weight: 500;
  font-size: 0.9rem;
  padding: 0.65rem 0.75rem;
  border-radius: 6px;
}

nav a:hover {
  background: rgba(255, 255, 255, 0.1);
}

nav a.router-link-active {
  background: rgba(255, 255, 255, 0.2);
  color: white;
  font-weight: 600;
}

.user-info {
  border-top: 1px solid rgba(255, 255, 255, 0.2);
  padding-top: 1rem;
  font-size: 0.85rem;
}

.user-info span {
  display: block;
  margin-bottom: 0.5rem;
}

.user-info button {
  width: 100%;
  background: rgba(255, 255, 255, 0.15);
  color: white;
  border: none;
  padding: 0.5rem;
  border-radius: 6px;
  cursor: pointer;
  font-family: var(--font-base);
  font-weight: 500;
}

.user-info button:hover {
  background: rgba(255, 255, 255, 0.25);
}

.content {
  flex: 1;
  background-image: url('../assets/staff-bg.jpg');
  background-size: cover;
  background-position: center;
  position: relative;
  overflow-y: auto;
}

.content-inner {
  position: relative;
  z-index: 1;
  padding: 2rem;
  max-width: 900px;
  margin: 0 auto;
}
</style>