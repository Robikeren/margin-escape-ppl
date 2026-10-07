<script setup>
import { ref, computed, onMounted } from 'vue'
import api from '../services/api'

const props = defineProps({
  jenisKopiId: { type: Number, required: true },
  namaKopi: { type: String, required: true },
})

const form = ref({
  tanggal: new Date().toISOString().slice(0, 10),
  stok_awal: 0,
  keluar: null,
  masuk: 0,
})

const loading = ref(false)
const loadingStokAwal = ref(false)
const successMsg = ref('')
const errorMsg = ref('')

const stokAkhirPreview = computed(() => {
  const awal = Number(form.value.stok_awal) || 0
  const keluar = Number(form.value.keluar) || 0
  const masuk = Number(form.value.masuk) || 0
  return awal - keluar + masuk
})

async function ambilStokAwalOtomatis() {
  loadingStokAwal.value = true
  try {
    const response = await api.get('/stok-opname', {
      params: { jenis_kopi_id: props.jenisKopiId },
    })
    const riwayat = response.data.data
    form.value.stok_awal = riwayat.length > 0 ? riwayat[0].stok_akhir : 0
  } catch (err) {
    errorMsg.value = 'Gagal mengambil data stok awal'
  } finally {
    loadingStokAwal.value = false
  }
}

onMounted(ambilStokAwalOtomatis)

async function handleSubmit() {
  errorMsg.value = ''
  successMsg.value = ''
  loading.value = true

  try {
    const response = await api.post('/stok-opname', {
      jenis_kopi_id: props.jenisKopiId,
      ...form.value,
    })
    successMsg.value = `Tersimpan. Stok akhir: ${response.data.data.stok_akhir} gram`

    form.value.keluar = null
    form.value.masuk = 0
    await ambilStokAwalOtomatis()
  } catch (err) {
    errorMsg.value = err.response?.data?.message || 'Gagal menyimpan data'
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <div class="kopi-card">
    <h2>{{ namaKopi }}</h2>

    <form @submit.prevent="handleSubmit">
      <label>Tanggal</label>
      <input v-model="form.tanggal" type="date" required />

      <label>Stok Awal (gram)</label>
      <input
        :value="loadingStokAwal ? 'Memuat...' : form.stok_awal"
        type="text"
        readonly
        class="readonly-field"
      />

      <label>Keluar / Terpakai (gram)</label>
      <input v-model.number="form.keluar" type="number" min="0" required />

      <label>Masuk / Restock (gram)</label>
      <input v-model.number="form.masuk" type="number" min="0" />

      <div class="preview">
        Stok Akhir: <strong>{{ stokAkhirPreview }} gram</strong>
      </div>

      <p v-if="errorMsg" class="error">{{ errorMsg }}</p>
      <p v-if="successMsg" class="success">{{ successMsg }}</p>

      <button type="submit" :disabled="loading">
        {{ loading ? 'Menyimpan...' : `Simpan ${namaKopi}` }}
      </button>
    </form>
  </div>
</template>

<style scoped>
.kopi-card {
  background: white;
  padding: 1.5rem;
  border-radius: 10px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.06);
  flex: 1;
  min-width: 280px;
}

.kopi-card h2 {
  margin: 0 0 16px;
  font-size: 1.1rem;
  color: var(--color-primary);
}

label {
  display: block;
  font-size: 0.8rem;
  font-weight: 500;
  margin-bottom: 4px;
  margin-top: 14px;
}

label:first-of-type {
  margin-top: 0;
}

input {
  width: 100%;
  padding: 0.6rem 0.75rem;
  border: 1px solid #ddd;
  border-radius: 6px;
  font-size: 0.9rem;
  font-family: var(--font-base);
  box-sizing: border-box;
}

input:focus {
  outline: none;
  border-color: var(--color-primary);
}

.readonly-field {
  background: #f0ece4;
  color: #6b5d4f;
  cursor: not-allowed;
}

.preview {
  margin-top: 16px;
  padding: 10px 12px;
  background: var(--color-cream);
  border-radius: 6px;
  font-size: 0.9rem;
}

button {
  width: 100%;
  margin-top: 16px;
  padding: 0.75rem;
  background: var(--color-primary);
  color: white;
  border: none;
  border-radius: 6px;
  font-weight: 600;
  cursor: pointer;
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
  font-size: 0.85rem;
  margin-top: 10px;
}

.success {
  color: #27824b;
  font-size: 0.85rem;
  margin-top: 10px;
}
</style>