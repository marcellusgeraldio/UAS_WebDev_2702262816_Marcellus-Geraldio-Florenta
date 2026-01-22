<template>
  <div class="dashboard-layout">
    <nav class="navbar">
      <div class="nav-brand">SAKTI Mini Dashboard</div>
      <div class="nav-user">
        <span>Admin Prodi</span>
        <button @click="logout" class="btn-logout">Logout</button>
      </div>
    </nav>

    <div class="content-container">
      <div class="card">
        <div class="card-header">
          <h2>Data Mahasiswa</h2>
          <button class="btn-primary" @click="goToCreate">+ Tambah Data</button>
        </div>

        <div class="toolbar">
          <div class="search-box">
            <input 
              type="text" 
              v-model="params.q" 
              @keyup.enter="fetchData"
              placeholder="Cari Nama / NIM..." 
            />
          </div>
          
          <div class="filter-box">
            <select v-model="params.prodi" @change="fetchData">
              <option value="">Semua Prodi</option>
              <option value="Teknik Informatika">Teknik Informatika</option>
              <option value="Sistem Informasi">Sistem Informasi</option>
              <option value="DKV">DKV</option>
            </select>

            <select v-model="params.status" @change="fetchData">
              <option value="">Semua Status</option>
              <option value="aktif">Aktif</option>
              <option value="cuti">Cuti</option>
              <option value="lulus">Lulus</option>
              <option value="dropout">DO</option>
            </select>
          </div>
        </div>

        <div class="table-responsive">
          <table>
            <thead>
              <tr>
                <th>NIM</th>
                <th>Nama Lengkap</th>
                <th>Prodi</th>
                <th>Angkatan</th>
                <th>Status</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              <tr v-if="loading">
                <td colspan="6" class="text-center">Memuat data...</td>
              </tr>
              <tr v-else-if="mahasiswas.length === 0">
                <td colspan="6" class="text-center">Tidak ada data ditemukan.</td>
              </tr>
              <tr v-else v-for="mhs in mahasiswas" :key="mhs.id">
                <td class="font-bold">{{ mhs.nim }}</td>
                <td>
                  <div class="user-info">
                    <span>{{ mhs.nama }}</span>
                    <small>{{ mhs.email }}</small>
                  </div>
                </td>
                <td>{{ mhs.prodi }}</td>
                <td>{{ mhs.angkatan }}</td>
                <td>
                  <span :class="['badge', mhs.status]">{{ mhs.status }}</span>
                </td>
                <td>
                  <button class="btn-icon edit" @click="editData(mhs.id)">Edit</button>
                  <button class="btn-icon delete" @click="deleteData(mhs.id)">Hapus</button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <div class="pagination">
          <span>Halaman {{ currentPage }} dari {{ lastPage }}</span>
          <div class="page-controls">
            <button :disabled="currentPage <= 1" @click="changePage(currentPage - 1)">Prev</button>
            <button :disabled="currentPage >= lastPage" @click="changePage(currentPage + 1)">Next</button>
          </div>
        </div>

      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      mahasiswas: [],
      loading: false,
      currentPage: 1,
      lastPage: 1,
      // Parameter untuk Filter & Search
      params: {
        q: '',
        prodi: '',
        status: '',
        page: 1
      }
    }
  },
  mounted() {
    this.fetchData();
  },
  methods: {
    async fetchData() {
      this.loading = true;
      try {
        const token = localStorage.getItem('token');
        
        // Request ke Backend dengan Parameter
        const response = await axios.get('http://127.0.0.1:8000/api/mahasiswa', {
          headers: { Authorization: `Bearer ${token}` },
          params: this.params
        });

        this.mahasiswas = response.data.data;
        this.currentPage = response.data.current_page;
        this.lastPage = response.data.last_page;
        
      } catch (error) {
        if (error.response && error.response.status === 401) {
            this.logout(); // Token expired, tendang keluar
        }
        console.error("Gagal ambil data", error);
      } finally {
        this.loading = false;
      }
    },
    changePage(page) {
        this.params.page = page;
        this.fetchData();
    },
    goToCreate() {
        this.$router.push('/mahasiswa/create');
    },
    editData(id) {
        this.$router.push(`/mahasiswa/edit/${id}`);
    },
async deleteData(id) {
        if(!confirm("Apakah Anda yakin ingin menghapus data ini?")) return;
        
        try {
            const token = localStorage.getItem('token');
            await axios.delete(`http://127.0.0.1:8000/api/mahasiswa/${id}`, {
                headers: { Authorization: `Bearer ${token}` }
            });
            
            alert("Data berhasil dihapus");
            this.fetchData(); // Refresh tabel otomatis
            
        } catch (error) {
            console.error(error);
            alert("Gagal menghapus data");
        }
    },
    async logout() {
      localStorage.removeItem('token');
      localStorage.removeItem('user');
      this.$router.push('/');
    }
  }
}
</script>

<style scoped>
/* Layout Dasar */
.dashboard-layout {
  background-color: #f8f9fd;
  min-height: 100vh;
  font-family: 'Segoe UI', sans-serif;
}

/* Navbar */
.navbar {
  background: white;
  padding: 15px 30px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}
.nav-brand { font-weight: bold; font-size: 18px; color: #bf00ff; }
.btn-logout { 
    background: transparent; border: 1px solid #ff4081; color: #ff4081; 
    padding: 5px 15px; border-radius: 20px; cursor: pointer; font-size: 12px; margin-left: 10px;
}

/* Container & Card */
.content-container { padding: 30px; max-width: 1000px; margin: 0 auto; }
.card { background: white; border-radius: 15px; padding: 25px; box-shadow: 0 5px 20px rgba(0,0,0,0.05); }

.card-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; }
.card-header h2 { margin: 0; font-size: 20px; color: #333; }

.btn-primary {
  background: linear-gradient(90deg, #d500f9, #bf00ff);
  color: white; border: none; padding: 10px 20px; border-radius: 8px; cursor: pointer; font-weight: bold;
}

/* Toolbar */
.toolbar { display: flex; gap: 15px; margin-bottom: 20px; flex-wrap: wrap; }
.search-box { flex-grow: 1; }
.search-box input { width: 100%; padding: 10px; border: 1px solid #eee; border-radius: 8px; outline: none; }
.filter-box select { padding: 10px; border: 1px solid #eee; border-radius: 8px; outline: none; background: white; }

/* Table Styling */
.table-responsive { overflow-x: auto; }
table { width: 100%; border-collapse: collapse; margin-top: 10px; }
th { text-align: left; padding: 15px; color: #888; font-size: 13px; border-bottom: 1px solid #eee; }
td { padding: 15px; border-bottom: 1px solid #f9f9f9; font-size: 14px; color: #444; vertical-align: middle; }

.user-info { display: flex; flex-direction: column; }
.user-info small { color: #888; font-size: 11px; }
.font-bold { font-weight: 600; }

/* Badge Status */
.badge { padding: 5px 10px; border-radius: 20px; font-size: 11px; font-weight: bold; text-transform: uppercase; }
.badge.aktif { background: #e3f2fd; color: #1976d2; }
.badge.cuti { background: #fff3e0; color: #f57c00; }
.badge.lulus { background: #e8f5e9; color: #388e3c; }
.badge.dropout { background: #ffebee; color: #c62828; }

/* Buttons */
.btn-icon { border: none; background: none; cursor: pointer; font-size: 12px; margin-right: 5px; }
.edit { color: #1976d2; }
.delete { color: #c62828; }

/* Pagination */
.pagination { display: flex; justify-content: space-between; align-items: center; margin-top: 20px; font-size: 13px; color: #666; }
.page-controls button {
    padding: 5px 15px; border: 1px solid #eee; background: white; border-radius: 5px; cursor: pointer; margin-left: 5px;
}
.page-controls button:disabled { background: #f5f5f5; color: #ccc; cursor: not-allowed; }
</style>