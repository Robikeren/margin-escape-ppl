<script setup>
import { ref, onMounted } from 'vue'
import api from '../../services/api'

const notifikasi = ref([])
const loading = ref(true)
const errorMsg = ref('')

async function ambilNotifikasi() {
  loading.value = true
  try {
    const response = await api.get('/notifikasi')
    notifikasi.value = response.data.data
  } catch (err) {
    errorMsg.value = 'Gagal mengambil notifikasi'
  } finally {
    loading.value = false
  }
}

async function tandaiDibaca(id) {
  try {
    await api.patch(`/notifikasi/${id}/dibaca`)
    await ambilNotifikasi()
  } catch (err) {
    errorMsg.value = 'Gagal menandai notifikasi'
  }
}

onMounted(ambilNotifikasi)
</script>

<template>
  <div class="notifikasi-page">
    <h1>Notifikasi</h1>
    <p class="subtitle">Peringatan stok menipis</p>

    <p v-if="loading">Memuat...</p>
    <p v-else-if="errorMsg" class="error">{{ errorMsg }}</p>
    <p v-else-if="notifikasi.length === 0">Belum ada notifikasi.</p>

    <div v-else class="notif-list">
      <div
        v-for="n in notifikasi"
        :key="n.id"
        class="notif-item"
        :class="{ unread: !n.status_dibaca }"
      >
        <div class="notif-content">
          <strong>{{ n.jenis_kopi }}</strong>
          <p>{{ n.pesan }}</p>
          <span class="date">{{ n.tanggal }}</span>
        </div>
        <button v-if="!n.status_dibaca" @click="tandaiDibaca(n.id)">
          Tandai dibaca
        </button>
      </div>
    </div>
  </div>
</template>

<style scoped>
.notifikasi-page h1 {
  margin-bottom: 2px;
  font-size: 1.4rem;
}

.subtitle {
  margin: 0 0 20px;
  color: #8a7a6d;
  font-size: 0.85rem;
}

.notif-list {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

.notif-item {
  background: white;
  padding: 1rem;
  border-radius: 8px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
  display: flex;
  justify-content: space-between;
  align-items: center;
  border-left: 3px solid transparent;
}

.notif-item.unread {
  border-left-color: var(--color-primary);
}

.notif-content p {
  margin: 4px 0;
  font-size: 0.85rem;
}

.date {
  font-size: 0.75rem;
  color: #8a7a6d;
}

.notif-item button {
  background: var(--color-primary);
  color: white;
  border: none;
  padding: 0.5rem 0.75rem;
  border-radius: 6px;
  cursor: pointer;
  font-size: 0.8rem;
  white-space: nowrap;
}

.notif-item button:hover {
  background: var(--color-primary-dark);
}

.error {
  color: #c0392b;
}
</style>