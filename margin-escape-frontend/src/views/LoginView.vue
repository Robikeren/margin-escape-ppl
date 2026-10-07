<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import logo from '../assets/logo.png'

const email = ref('')
const password = ref('')
const errorMsg = ref('')
const loading = ref(false)

const router = useRouter()
const authStore = useAuthStore()

async function handleLogin() {
  errorMsg.value = ''
  loading.value = true

  try {
    await authStore.login(email.value, password.value)
    if (authStore.isOwner) {
    router.push('/dashboard')
    } else {
    router.push('/input-stok')
    }
  } catch (err) {
    errorMsg.value = err.response?.data?.message || 'Login gagal, coba lagi'
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <div class="login-page">

    <img :src="logo" alt="Margin Escape" class="logo" />

    <div class="login-card">
      <h1>Masuk</h1>
      <p class="subtitle">Inventory Forecasting System</p>

      <form @submit.prevent="handleLogin">
        <label for="email">Email</label>
        <input id="email" v-model="email" type="email" placeholder="nama@marginescape.com" required />

        <label for="password">Kata Sandi</label>
        <input id="password" v-model="password" type="password" placeholder="••••••••" required />

        <p v-if="errorMsg" class="error">{{ errorMsg }}</p>

        <button type="submit" :disabled="loading">
          {{ loading ? 'Memproses...' : 'Masuk' }}
        </button>
      </form>
    </div>
  </div>
</template>

<style scoped>
.login-page {
  position: relative;
  height: 100vh;
  width: 100%;
  background-image: url('../assets/login-bg.jpg');
  background-size: cover;
  background-position: center;
  display: flex;
  align-items: center;
  justify-content: center;
}

.logo {
  position: absolute;
  top: 24px;
  left: 32px;
  height: 48px;
  z-index: 2;
}

.login-card {
  position: relative;
  z-index: 2;
  background: white;
  padding: 2.5rem 2rem;
  border-radius: 12px;
  box-shadow: 0 8px 30px rgba(0, 0, 0, 0.2);
  width: 340px;
}

.login-card h1 {
  margin: 0 0 4px;
  font-size: 1.5rem;
  font-weight: 700;
  color: var(--color-coffee-dark);
}

.subtitle {
  margin: 0 0 24px;
  font-size: 0.85rem;
  color: #8a7a6d;
}

label {
  display: block;
  font-size: 0.8rem;
  font-weight: 500;
  margin-bottom: 4px;
  color: var(--color-coffee-dark);
}

input {
  width: 100%;
  padding: 0.65rem 0.75rem;
  margin-bottom: 16px;
  border: 1px solid #ddd;
  border-radius: 6px;
  font-size: 0.9rem;
  font-family: var(--font-base);
}

input:focus {
  outline: none;
  border-color: var(--color-primary);
}

button {
  width: 100%;
  padding: 0.75rem;
  background: var(--color-primary);
  color: white;
  border: none;
  border-radius: 6px;
  font-size: 0.95rem;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.15s ease;
}

button:hover:not(:disabled) {
  background: var(--color-primary-dark);
}

button:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.error {
  color: #c0392b;
  font-size: 0.8rem;
  margin: -8px 0 12px;
}
</style>