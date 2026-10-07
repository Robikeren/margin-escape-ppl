<script setup>
import { ref, onMounted } from 'vue'
import api from '../../services/api'

const riwayat = ref([])
const loading = ref(true)
const errorMsg = ref('')

async function ambilRiwayat() {
  loading.value = true
  try {
    const response = await api.get('/stok-opname')
    riwayat.value = response.data.data
  } catch (err) {
    errorMsg.value = 'Gagal mengambil data riwayat'
  } finally {
    loading.value = false
  }
}

onMounted(ambilRiwayat)
</script>

<template>
  <div class="riwayat-page">
    <h1>Riwayat Stok Opname</h1>
    <p class="subtitle">Semua data stok yang pernah diinput</p>

    <p v-if="loading">Memuat data...</p>
    <p v-else-if="errorMsg" class="error">{{ errorMsg }}</p>

    <table v-else class="riwayat-table">
      <thead>
        <tr>
          <th>Tanggal</th>
          <th>Jenis Kopi</th>
          <th>Stok Awal</th>
          <th>Keluar</th>
          <th>Masuk</th>
          <th>Stok Akhir</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="item in riwayat" :key="item.id">
          <td>{{ item.tanggal }}</td>
          <td>{{ item.jenis_kopi }}</td>
          <td>{{ item.stok_awal }}</td>
          <td>{{ item.keluar }}</td>
          <td>{{ item.masuk }}</td>
          <td><strong>{{ item.stok_akhir }}</strong></td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<style scoped>
.riwayat-page h1 {
  margin-bottom: 2px;
  font-size: 1.4rem;
}

.subtitle {
  margin: 0 0 20px;
  color: #8a7a6d;
  font-size: 0.85rem;
}

.riwayat-table {
  width: 100%;
  border-collapse: collapse;
  background: white;
  border-radius: 10px;
  overflow: hidden;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.06);
}

.riwayat-table th,
.riwayat-table td {
  padding: 0.7rem 1rem;
  text-align: left;
  font-size: 0.85rem;
  border-bottom: 1px solid #eee;
}

.riwayat-table th {
  background: var(--color-cream);
  font-weight: 600;
}

.error {
  color: #c0392b;
}
</style>