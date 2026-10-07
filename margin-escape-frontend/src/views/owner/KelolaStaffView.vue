<script setup>
import { ref, onMounted } from 'vue'
import api from '../../services/api'

const daftarStaff = ref([])
const loading = ref(true)
const errorMsg = ref('')

const form = ref({
  nama: '',
  email: '',
  password: '',
  role: 'staff',
})

const submitting = ref(false)
const formError = ref('')
const formSuccess = ref('')

async function ambilDaftarStaff() {
  loading.value = true
  try {
    const response = await api.get('/staff')
    daftarStaff.value = response.data.data
  } catch (err) {
    errorMsg.value = 'Gagal mengambil daftar staff'
  } finally {
    loading.value = false
  }
}

async function handleTambahStaff() {
  formError.value = ''
  formSuccess.value = ''
  submitting.value = true

  try {
    await api.post('/users', form.value)
    formSuccess.value = `Akun staff "${form.value.nama}" berhasil dibuat`
    form.value = { nama: '', email: '', password: '', role: 'staff' }
    await ambilDaftarStaff()
  } catch (err) {
    formError.value = err.response?.data?.message || 'Gagal membuat akun staff'
  } finally {
    submitting.value = false
  }
}

onMounted(ambilDaftarStaff)
</script>

<template>
  <div class="kelola-staff-page">
    <h1>Kelola Akun Staff</h1>
    <p class="subtitle">Lihat dan tambah akun staff/barista</p>

    <div class="layout-grid">
      <div class="list-section">
        <h3>Daftar Staff</h3>

        <p v-if="loading">Memuat...</p>
        <p v-else-if="errorMsg" class="error">{{ errorMsg }}</p>
        <p v-else-if="daftarStaff.length === 0">Belum ada akun staff.</p>

        <table v-else class="staff-table">
          <thead>
            <tr>
              <th>Nama</th>
              <th>Email</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="s in daftarStaff" :key="s.id">
              <td>{{ s.nama }}</td>
              <td>{{ s.email }}</td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="form-section">
        <h3>Tambah Staff Baru</h3>

        <form @submit.prevent="handleTambahStaff" class="form-card">
          <label>Nama</label>
          <input v-model="form.nama" type="text" required />

          <label>Email</label>
          <input v-model="form.email" type="email" required />

          <label>Password</label>
          <input v-model="form.password" type="password" minlength="6" required />

          <p v-if="formError" class="error">{{ formError }}</p>
          <p v-if="formSuccess" class="success">{{ formSuccess }}</p>

          <button type="submit" :disabled="submitting">
            {{ submitting ? 'Menyimpan...' : 'Tambah Staff' }}
          </button>
        </form>
      </div>
    </div>
  </div>
</template>

<style scoped>
.kelola-staff-page h1 {
  margin-bottom: 2px;
  font-size: 1.4rem;
}

.subtitle {
  margin: 0 0 20px;
  color: #8a7a6d;
  font-size: 0.85rem;
}

.layout-grid {
  display: flex;
  gap: 1.5rem;
  flex-wrap: wrap;
  align-items: flex-start;
}

.list-section {
  flex: 2;
  min-width: 300px;
  background: white;
  padding: 1.5rem;
  border-radius: 10px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.06);
}

.list-section h3 {
  margin: 0 0 16px;
  font-size: 1rem;
}

.staff-table {
  width: 100%;
  border-collapse: collapse;
}

.staff-table th,
.staff-table td {
  padding: 0.6rem 0.75rem;
  text-align: left;
  font-size: 0.85rem;
  border-bottom: 1px solid #eee;
}

.staff-table th {
  background: var(--color-cream);
}

.form-section {
  flex: 1;
  min-width: 260px;
}

.form-card {
  background: white;
  padding: 1.5rem;
  border-radius: 10px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.06);
}

.form-card h3 {
  margin: 0 0 16px;
  font-size: 1rem;
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

button {
  width: 100%;
  margin-top: 16px;
  padding: 0.7rem;
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
}

.error {
  color: #c0392b;
  font-size: 0.85rem;
}

.success {
  color: #27824b;
  font-size: 0.85rem;
}
</style>