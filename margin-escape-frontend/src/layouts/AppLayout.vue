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
    <header class="topbar">
      <img :src="logo" alt="Margin Escape" class="logo" />

      <nav>
        <router-link to="/input-stok">Input Stok</router-link>
      </nav>

      <div class="user-info">
        <span>{{ authStore.user?.nama }}</span>
        <button @click="handleLogout">Keluar</button>
      </div>
    </header>

    <main class="content">
      <router-view />
    </main>
  </div>
</template>

<style scoped>
.app-layout {
  min-height: 100vh;
  background: var(--color-cream);
}

.topbar {
  display: flex;
  align-items: center;
  justify-content: space-between;
  background: white;
  padding: 0.75rem 2rem;
  box-shadow: 0 1px 4px rgba(0, 0, 0, 0.08);
}

.logo {
  height: 36px;
}

nav a {
  color: var(--color-coffee-dark);
  text-decoration: none;
  font-weight: 500;
  margin-right: 1.5rem;
  font-size: 0.9rem;
}

nav a.router-link-active {
  color: var(--color-primary);
  font-weight: 600;
}

.user-info {
  display: flex;
  align-items: center;
  gap: 1rem;
  font-size: 0.85rem;
  color: var(--color-coffee-dark);
}

.user-info button {
  background: var(--color-primary);
  color: white;
  border: none;
  padding: 0.5rem 1rem;
  border-radius: 6px;
  cursor: pointer;
  font-family: var(--font-base);
  font-weight: 500;
}

.user-info button:hover {
  background: var(--color-primary-dark);
}

.content {
  padding: 2rem;
  max-width: 900px;
  margin: 0 auto;
}
</style>