<script setup>
import { ref, onMounted } from 'vue'
import api from '../../services/api'

const jenisKopiOptions = [
  { id: 1, nama: 'Robusta' },
  { id: 2, nama: 'Arabika' },
]

const selectedJenisKopi = ref(1)
const hasil = ref(null)
const loading = ref(true)
const errorMsg = ref('')

async function ambilForecasting() {
  loading.value = true
  errorMsg.value = ''
  hasil.value = null

  try {
    const response = await api.get('/forecasting/rekomendasi', {
      params: { jenis_kopi_id: selectedJenisKopi.value },
    })
    hasil.value = response.data.data
  } catch (err) {
    errorMsg.value = err.response?.data?.message || 'Gagal mengambil data forecasting. Pastikan service ML (Python) sedang berjalan.'
  } finally {
    loading.value = false
  }
}

onMounted(ambilForecasting)
</script>

<template>
  <div class="forecasting-page">
    <h1>Forecasting & Rekomendasi Restock</h1>
    <p class="subtitle">Prediksi kebutuhan stok berbasis Machine Learning</p>

    <div class="selector-row">
      <button
        v-for="jk in jenisKopiOptions"
        :key="jk.id"
        :class="{ active: selectedJenisKopi === jk.id }"
        @click="selectedJenisKopi = jk.id; ambilForecasting()"
      >
        {{ jk.nama }}
      </button>
    </div>

    <p v-if="loading">Menghitung prediksi...</p>
    <p v-else-if="errorMsg" class="error">{{ errorMsg }}</p>

    <div v-else-if="hasil" class="result-grid">
      <div class="summary-card">
        <div class="summary-item">
          <span class="label">Stok Saat Ini</span>
          <span class="value">{{ hasil.stok_saat_ini }} gram</span>
        </div>
        <div class="summary-item">
          <span class="label">Prediksi Kebutuhan 7 Hari</span>
          <span class="value">{{ hasil.prediksi_kebutuhan_7_hari }} gram</span>
        </div>
        <div class="summary-item">
          <span class="label">Estimasi Habis Pada</span>
          <span class="value">{{ hasil.estimasi_habis_pada }}</span>
        </div>
        <div class="model-badge">{{ hasil.model_ml }}</div>
      </div>

      <div
        class="recommendation-card"
        :class="hasil.rekomendasi_restock.perlu_restock ? 'urgent' : 'safe'"
      >
        <h3>
          {{ hasil.rekomendasi_restock.perlu_restock ? '⚠ Perlu Restock' : '✓ Stok Masih Aman' }}
        </h3>
        <p v-if="hasil.rekomendasi_restock.perlu_restock">
          Disarankan restock sebanyak
          <strong>{{ hasil.rekomendasi_restock.jumlah_disarankan }} gram</strong>
        </p>
        <p v-else>Belum perlu restock dalam waktu dekat.</p>
      </div>

      <div class="daily-table-wrapper">
        <h3>Prediksi Harian (7 Hari ke Depan)</h3>
        <table class="daily-table">
          <thead>
            <tr>
              <th>Tanggal</th>
              <th>Prediksi Keluar (gram)</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="p in hasil.prediksi_harian" :key="p.tanggal">
              <td>{{ p.tanggal }}</td>
              <td>{{ p.prediksi_keluar }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<style scoped>
.forecasting-page h1 {
  margin-bottom: 2px;
  font-size: 1.4rem;
}

.subtitle {
  margin: 0 0 20px;
  color: #8a7a6d;
  font-size: 0.85rem;
}

.selector-row {
  display: flex;
  gap: 0.5rem;
  margin-bottom: 1.5rem;
}

.selector-row button {
  padding: 0.5rem 1.25rem;
  border: 1px solid var(--color-primary);
  background: white;
  color: var(--color-primary);
  border-radius: 6px;
  cursor: pointer;
  font-weight: 500;
  font-family: var(--font-base);
}

.selector-row button.active {
  background: var(--color-primary);
  color: white;
}

.result-grid {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.summary-card {
  background: white;
  padding: 1.5rem;
  border-radius: 10px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.06);
  display: flex;
  gap: 2rem;
  flex-wrap: wrap;
  align-items: center;
  position: relative;
}

.summary-item {
  display: flex;
  flex-direction: column;
}

.summary-item .label {
  font-size: 0.75rem;
  color: #8a7a6d;
}

.summary-item .value {
  font-size: 1.2rem;
  font-weight: 700;
  color: var(--color-coffee-dark);
}

.model-badge {
  margin-left: auto;
  font-size: 0.7rem;
  background: var(--color-cream);
  padding: 4px 10px;
  border-radius: 12px;
  color: #8a7a6d;
}

.recommendation-card {
  padding: 1.25rem 1.5rem;
  border-radius: 10px;
  color: white;
}

.recommendation-card.urgent {
  background: #c0392b;
}

.recommendation-card.safe {
  background: #27824b;
}

.recommendation-card h3 {
  margin: 0 0 6px;
}

.recommendation-card p {
  margin: 0;
  font-size: 0.9rem;
}

.daily-table-wrapper {
  background: white;
  padding: 1.5rem;
  border-radius: 10px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.06);
}

.daily-table-wrapper h3 {
  margin: 0 0 12px;
  font-size: 1rem;
}

.daily-table {
  width: 100%;
  border-collapse: collapse;
}

.daily-table th,
.daily-table td {
  padding: 0.6rem 0.75rem;
  text-align: left;
  font-size: 0.85rem;
  border-bottom: 1px solid #eee;
}

.daily-table th {
  background: var(--color-cream);
}

.error {
  color: #c0392b;
}
</style>