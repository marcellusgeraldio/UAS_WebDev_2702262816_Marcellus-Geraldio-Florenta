import { createApp } from 'vue'
import './style.css'
import App from './App.vue'
import router from './router'

const app = createApp(App)

app.use(router) // <--- INI WAJIB ADA SUPAYA TIDAK BLANK
app.mount('#app')