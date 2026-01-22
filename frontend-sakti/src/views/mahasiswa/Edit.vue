<template>
  <div class="dashboard-layout">
    <div class="content-container">
      <div class="card">
        <div class="card-header">
          <h2>Edit Data Mahasiswa</h2>
          <button class="btn-back" @click="$router.push('/mahasiswa')">Kembali</button>
        </div>

        <div v-if="isLoadingFetch" class="text-center">Mengambil data...</div>

        <form v-else @submit.prevent="updateData">
          
          <div v-if="errorMessage" class="alert-error">{{ errorMessage }}</div>

          <div class="form-grid">
            <div class="form-group">
              <label>NIM (Tidak bisa diubah)</label>
              <input type="text" v-model="form.nim" readonly class="bg-gray" />
            </div>

            <div class="form-group">
              <label>Nama Lengkap</label>
              <input type="text" v-model="form.nama" required />
              <small v-if="errors.nama" class="text-danger">{{ errors.nama[0] }}</small>
            </div>

            <div class="form-group">
              <label>Email Kampus</label>
              <input type="email" v-model="form.email" required />
              <small v-if="errors.email" class="text-danger">{{ errors.email[0] }}</small>
            </div>

            <div class="form-group">
              <label>Angkatan</label>
              <input type="number" v-model="form.angkatan" required />
              <small v-if="errors.angkatan" class="text-danger">{{ errors.angkatan[0] }}</small>
            </div>

            <div class="form-group">
              <label>Program Studi</label>
              <select v-model="form.prodi" required>
                <option value="Teknik Informatika">Teknik Informatika</option>
                <option value="Sistem Informasi">Sistem Informasi</option>
                <option value="DKV">DKV</option>
                <option value="Manajemen">Manajemen</option>
              </select>
            </div>

            <div class="form-group">
              <label>Status Mahasiswa</label>
              <select v-model="form.status" required>
                <option value="aktif">Aktif</option>
                <option value="cuti">Cuti</option>
                <option value="lulus">Lulus</option>
                <option value="dropout">Drop Out</option>
              </select>
            </div>
          </div>

          <div class="form-actions">
            <button type="submit" class="btn-submit" :disabled="isSaving">
              {{ isSaving ? 'Menyimpan...' : 'Update Data' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
import { useRoute, useRouter } from 'vue-router'; // Pakai composables Vue 3

export default {
  data() {
    return {
      form: {
        nim: '', nama: '', email: '', prodi: '', angkatan: '', status: ''
      },
      errors: {},
      errorMessage: '',
      isLoadingFetch: true,
      isSaving: false,
      id: null
    }
  },
  mounted() {
    // Ambil ID dari URL
    this.id = this.$route.params.id;
    this.getDetail();
  },
  methods: {
    async getDetail() {
      try {
        const token = localStorage.getItem('token');
        const response = await axios.get(`http://127.0.0.1:8000/api/mahasiswa/${this.id}`, {
          headers: { Authorization: `Bearer ${token}` }
        });
        this.form = response.data; // Isi form dengan data lama
      } catch (error) {
        alert("Data tidak ditemukan");
        this.$router.push('/mahasiswa');
      } finally {
        this.isLoadingFetch = false;
      }
    },
    async updateData() {
      this.isSaving = true;
      this.errors = {};
      
      try {
        const token = localStorage.getItem('token');
        await axios.put(`http://127.0.0.1:8000/api/mahasiswa/${this.id}`, this.form, {
          headers: { Authorization: `Bearer ${token}` }
        });

        alert("Data berhasil diupdate!");
        this.$router.push('/mahasiswa');

      } catch (error) {
        if (error.response && error.response.status === 422) {
            this.errors = error.response.data.errors;
        } else {
            this.errorMessage = "Gagal update data.";
        }
      } finally {
        this.isSaving = false;
      }
    }
  }
}
</script>

<style scoped>

.dashboard-layout { background-color: #f8f9fd; min-height: 100vh; font-family: 'Segoe UI', sans-serif; padding-top: 50px; }
.content-container { max-width: 800px; margin: 0 auto; padding: 20px; }
.card { background: white; border-radius: 15px; padding: 30px; box-shadow: 0 5px 20px rgba(0,0,0,0.05); }
.card-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; border-bottom: 1px solid #eee; padding-bottom: 15px; }
.card-header h2 { margin: 0; font-size: 22px; color: #333; }
.form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
.form-group { margin-bottom: 15px; }
label { display: block; font-weight: 600; font-size: 13px; color: #555; margin-bottom: 8px; }
input, select { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; box-sizing: border-box; }
input:focus, select:focus { border-color: #bf00ff; outline: none; }
.bg-gray { background-color: #f0f0f0; color: #888; cursor: not-allowed; } /* Style Readonly */
.text-danger { color: #d32f2f; font-size: 12px; margin-top: 5px; display: block; }
.btn-back { background: transparent; border: 1px solid #ddd; padding: 8px 15px; border-radius: 5px; cursor: pointer; }
.btn-submit { width: 100%; padding: 14px; background: linear-gradient(90deg, #d500f9, #bf00ff); color: white; border: none; border-radius: 8px; font-weight: bold; cursor: pointer; margin-top: 20px; }
@media (max-width: 600px) { .form-grid { grid-template-columns: 1fr; } }
</style>