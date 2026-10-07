<script setup>
import { ref, onMounted, watch } from 'vue'
import { Line } from 'vue-chartjs'
import {
  Chart as ChartJS,
  Title,
  Tooltip,
  Legend,
  LineElement,
  CategoryScale,
  LinearScale,
  PointElement,
} from 'chart.js'
import api from '../services/api'

ChartJS.register(Title, Tooltip, Legend, LineElement, CategoryScale, LinearScale, PointElement)

const props = defineProps({
  jumlahHari: { type: Number, default: 30 },
})

const chartData = ref({ labels: [], datasets: [] })
const loading = ref(true)
const errorMsg = ref('')

const chartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: { position: 'top' },
  },
  scales: {
    y: {
      beginAtZero: true,
      title: { display: true, text: 'Gram' },
    },
  },
}

async function ambilDataTren() {
  loading.value = true
  errorMsg.value = ''

  try {
    const startDate = new Date()
    startDate.setDate(startDate.getDate() - props.jumlahHari)
    const start = startDate.toISOString().slice(0, 10)

    const [robustaRes, arabikaRes] = await Promise.all([
      api.get('/stok-opname', { params: { jenis_kopi_id: 1, start } }),
      api.get('/stok-opname', { params: { jenis_kopi_id: 2, start } }),
    ])

    // Data dari API urut descending (terbaru dulu), kita balik jadi ascending buat chart
    const robustaData = robustaRes.data.data.slice().reverse()
    const arabikaData = arabikaRes.data.data.slice().reverse()

    // Gabungkan semua tanggal unik dari kedua jenis kopi, urutkan
    const semuaTanggal = [...new Set([
      ...robustaData.map((d) => d.tanggal),
      ...arabikaData.map((d) => d.tanggal),
    ])].sort()

    const robustaMap = Object.fromEntries(robustaData.map((d) => [d.tanggal, d.keluar]))
    const arabikaMap = Object.fromEntries(arabikaData.map((d) => [d.tanggal, d.keluar]))

    chartData.value = {
      labels: semuaTanggal,
      datasets: [
        {
          label: 'Robusta',
          data: semuaTanggal.map((t) => robustaMap[t] ?? null),
          borderColor: '#D9631E',
          backgroundColor: '#D9631E',
          tension: 0.3,
        },
        {
          label: 'Arabika',
          data: semuaTanggal.map((t) => arabikaMap[t] ?? null),
          borderColor: '#2E1F17',
          backgroundColor: '#2E1F17',
          tension: 0.3,
        },
      ],
    }
  } catch (err) {
    errorMsg.value = 'Gagal mengambil data tren konsumsi'
  } finally {
    loading.value = false
  }
}

onMounted(ambilDataTren)
watch(() => props.jumlahHari, ambilDataTren)
</script>

<template>
  <div class="chart-card">
    <h3>Tren Konsumsi Harian ({{ jumlahHari }} Hari Terakhir)</h3>

    <p v-if="loading">Memuat grafik...</p>
    <p v-else-if="errorMsg" class="error">{{ errorMsg }}</p>

    <div v-else class="chart-wrapper">
      <Line :data="chartData" :options="chartOptions" />
    </div>
  </div>
</template>

<style scoped>
.chart-card {
  background: white;
  padding: 1.5rem;
  border-radius: 10px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.06);
}

.chart-card h3 {
  margin: 0 0 16px;
  font-size: 1rem;
  color: var(--color-coffee-dark);
}

.chart-wrapper {
  height: 300px;
}

.error {
  color: #c0392b;
}
</style>