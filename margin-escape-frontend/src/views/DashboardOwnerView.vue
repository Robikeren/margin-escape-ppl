<script setup>
import { ref, onMounted } from 'vue'
import api from '../services/api'
import TrenKonsumsiChart from '../components/TrenKonsumsiChart.vue'

const ringkasan = ref({})
const loading = ref(true)
const errorMsg = ref('')

const statusLabel = {
  aman: 'Aman',
  menipis: 'Menipis',
  kritis: 'Kritis',
  belum_ada_data: 'Belum Ada Data',
}

async function ambilRingkasan() {
  loading.value = true
  try {
    const response = await api.get('/dashboard/ringkasan')
    ringkasan.value = response.data.data
  } catch (err) {
    errorMsg.value = 'Gagal mengambil data dashboard'
  } finally {
    loading.value = false
  }
}

onMounted(ambilRingkasan)
</script>

<template>
  <div class="dashboard-page">
    <h1>Dashboard</h1>
    <p class="subtitle">Ringkasan status stok kopi saat ini</p>

    <p v-if="loading">Memuat data...</p>
    <p v-else-if="errorMsg" class="error">{{ errorMsg }}</p>

    <div v-else class="cards-row">
      <div
        v-for="(data, jenisKopi) in ringkasan"
        :key="jenisKopi"
        class="status-card"
        :class="`status-${data.status}`"
      >
        <h2>{{ jenisKopi.charAt(0).toUpperCase() + jenisKopi.slice(1) }}</h2>

        <div class="status-badge" :class="`badge-${data.status}`">
          {{ statusLabel[data.status] }}
        </div>

        <div class="stat-row">
          <span class="stat-label">Stok Saat Ini</span>
          <span class="stat-value">{{ data.stok_saat_ini }} gram</span>
        </div>

        <div v-if="data.rata_rata_konsumsi_harian" class="stat-row">
          <span class="stat-label">Rata-rata Konsumsi Harian</span>
          <span class="stat-value">{{ data.rata_rata_konsumsi_harian }} gram</span>
        </div>
      </div>
    </div>

    <div class="chart-section">
      <TrenKonsumsiChart :jumlah-hari="30" />
    </div>
  </div>
</template>

<style scoped>
.dashboard-page h1 {
  margin-bottom: 2px;
  font-size: 1.4rem;
}

.subtitle {
  margin: 0 0 20px;
  color: #8a7a6d;
  font-size: 0.85rem;
}

.cards-row {
  display: flex;
  gap: 1.5rem;
  flex-wrap: wrap;
}

.status-card {
  background: white;
  padding: 1.5rem;
  border-radius: 10px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.06);
  flex: 1;
  min-width: 260px;
  border-top: 4px solid #ccc;
}

.status-card.status-aman {
  border-top-color: #27824b;
}
.status-card.status-menipis {
  border-top-color: #d9a124;
}
.status-card.status-kritis {
  border-top-color: #c0392b;
}
.status-card.status-belum_ada_data {
  border-top-color: #999;
}

.status-card h2 {
  margin: 0 0 10px;
  font-size: 1.1rem;
  color: var(--color-coffee-dark);
}

.status-badge {
  display: inline-block;
  padding: 0.25rem 0.75rem;
  border-radius: 20px;
  font-size: 0.75rem;
  font-weight: 600;
  margin-bottom: 16px;
}

.badge-aman {
  background: #e6f4ea;
  color: #27824b;
}
.badge-menipis {
  background: #fdf1d8;
  color: #a87a0f;
}
.badge-kritis {
  background: #fbe4e1;
  color: #c0392b;
}
.badge-belum_ada_data {
  background: #eee;
  color: #777;
}

.stat-row {
  display: flex;
  justify-content: space-between;
  padding: 8px 0;
  border-top: 1px solid #f0ece4;
  font-size: 0.85rem;
}

.stat-label {
  color: #8a7a6d;
}

.stat-value {
  font-weight: 600;
  color: var(--color-coffee-dark);
}

.error {
  color: #c0392b;
}

.chart-section {
  margin-top: 1.5rem;
}
</style>