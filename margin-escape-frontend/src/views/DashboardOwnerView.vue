<script setup>
import { useAuthStore } from '../stores/auth'
const authStore = useAuthStore()
</script>

<template>
  <div style="padding: 2rem;">
    <h1>Dashboard</h1>
    <p>Selamat datang, {{ authStore.user?.nama }} ({{ authStore.user?.role }})</p>
    <button @click="authStore.logout().then(() => $router.push('/login'))">Logout</button>
  </div>
</template>