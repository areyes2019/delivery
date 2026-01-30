import '../css/dashboard.css'
import axios from 'axios'
import Echo from 'laravel-echo'
import Pusher from 'pusher-js'   // 👈 AÑADIR
import { createApp } from 'vue'

// Bootstrap 5
import 'bootstrap/dist/css/bootstrap.min.css'
import 'bootstrap/dist/js/bootstrap.bundle.min.js'
import 'bootstrap-icons/font/bootstrap-icons.css'

import DashboardLayout from './components/dashboard/DashboardLayout.vue'

// 🌐 Axios global
window.axios = axios
axios.defaults.baseURL = '/api'
axios.defaults.withCredentials = true
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest'

// CSRF
const token = document.head.querySelector('meta[name="csrf-token"]')
if (token) {
  axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content
}

// 👇🔥 ESTA LÍNEA ES CRÍTICA 🔥👇
window.Pusher = Pusher

// 🚀 Echo + Reverb
window.Echo = new Echo({
  broadcaster: 'reverb',
  key: import.meta.env.VITE_REVERB_APP_KEY,
  wsHost: import.meta.env.VITE_REVERB_HOST,
  wsPort: Number(import.meta.env.VITE_REVERB_PORT),
  forceTLS: false,
  encrypted: false,
  disableStats: true,
})

console.log('🟢 Echo inicializado correctamente')

// 🚀 App
createApp(DashboardLayout).mount('#app')
