<template>
  <div class="login-container">
    <div class="login-card">
      <div class="card-header">
        <h2 class="brand-title">SAKTI Mini</h2>
        <h3>Log in</h3>
        <div class="title-underline"></div>
      </div>

      <div v-if="errorMessage" class="error-alert">
        {{ errorMessage }}
      </div>

      <form @submit.prevent="handleLogin">
        <div class="form-group">
          <label>Email</label>
          <input 
            type="email" 
            v-model="email" 
            placeholder="example@kampus.ac.id" 
            required
          />
        </div>

        <div class="form-group">
          <label>Password</label>
          <input 
            type="password" 
            v-model="password" 
            placeholder="Enter your password" 
            required
          />
        </div>

        <button type="submit" class="btn-submit" :disabled="isLoading">
          <span v-if="isLoading">Loading...</span>
          <span v-else>Submit</span>
        </button>
      </form>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      email: '',
      password: '',
      errorMessage: '',
      isLoading: false
    }
  },
  methods: {
    async handleLogin() {
      this.isLoading = true;
      this.errorMessage = '';

      try {
        // Tembak API Backend
        const response = await axios.post('http://127.0.0.1:8000/api/login', {
          email: this.email,
          password: this.password
        });

        // Simpan Token & User ke LocalStorage (Agar browser ingat kita sudah login)
        localStorage.setItem('token', response.data.access_token);
        localStorage.setItem('user', JSON.stringify(response.data.user));

        // Pindah ke Halaman Mahasiswa (Dashboard)
        this.$router.push('/mahasiswa');

      } catch (error) {
        // Tangkap Error dari Backend (Misal: Kredensial Salah)
        if (error.response && error.response.data.errors) {
            // Ambil pesan error pertama
            this.errorMessage = Object.values(error.response.data.errors).flat()[0]; 
        } else if (error.response && error.response.data.message) {
            this.errorMessage = error.response.data.message;
        } else {
            this.errorMessage = 'Gagal terhubung ke server.';
        }
      } finally {
        this.isLoading = false;
      }
    }
  }
}
</script>

<style scoped>
/* 1. Layout Utama (Tengah Layar) */
.login-container {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 100vh;
  background-color: #f8f9fd; /* Background abu muda bersih */
  font-family: 'Segoe UI', sans-serif;
}

/* 2. Kartu Login (Mirip Gambar) */
.login-card {
  background: white;
  width: 100%;
  max-width: 400px;
  padding: 40px;
  border-radius: 20px; /* Sudut membulat besar */
  box-shadow: 0 10px 25px rgba(112, 0, 255, 0.1); /* Bayangan ungu halus */
  text-align: center;
}

/* 3. Header & Judul */
.brand-title {
  font-size: 14px;
  color: #666;
  margin-bottom: 5px;
  text-transform: uppercase;
  letter-spacing: 1px;
}

h3 {
  font-size: 28px;
  font-weight: bold;
  color: #333;
  margin: 0;
}

.title-underline {
  width: 40px;
  height: 4px;
  background-color: #bf00ff; /* Ungu sesuai gambar */
  margin: 10px auto 30px;
  border-radius: 2px;
}

/* 4. Input Form */
.form-group {
  text-align: left;
  margin-bottom: 20px;
}

label {
  display: block;
  font-size: 14px;
  color: #333;
  margin-bottom: 8px;
  font-weight: 600;
}

input {
  width: 100%;
  padding: 12px 15px;
  border: 1px solid #eee;
  background-color: #fcfcfc;
  border-radius: 10px; /* Sudut input membulat */
  font-size: 14px;
  outline: none;
  transition: all 0.3s;
  box-sizing: border-box; /* Agar padding tidak merusak lebar */
}

input:focus {
  border-color: #bf00ff;
  background-color: white;
  box-shadow: 0 0 0 3px rgba(191, 0, 255, 0.1);
}

/* 5. Tombol Submit (Ungu Neon) */
.btn-submit {
  width: 100%;
  padding: 14px;
  background: linear-gradient(90deg, #d500f9, #bf00ff); /* Gradasi Ungu */
  color: white;
  border: none;
  border-radius: 25px; /* Bulat kapsul */
  font-size: 16px;
  font-weight: bold;
  cursor: pointer;
  transition: transform 0.2s, box-shadow 0.2s;
  margin-top: 10px;
}

.btn-submit:hover {
  transform: translateY(-2px);
  box-shadow: 0 5px 15px rgba(191, 0, 255, 0.4);
}

.btn-submit:disabled {
  background: #ccc;
  cursor: not-allowed;
}

/* 6. Pesan Error */
.error-alert {
  background-color: #ffebee;
  color: #c62828;
  padding: 10px;
  border-radius: 8px;
  font-size: 13px;
  margin-bottom: 20px;
  border: 1px solid #ffcdd2;
}
</style>