import '../css/dashboard.css'
import axios from 'axios'
import Echo from 'laravel-echo'
import Pusher from 'pusher-js'
import { createApp } from 'vue'

// Bootstrap
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

// 🔑 Pusher global
window.Pusher = Pusher

// 🚀 Echo + PUSHER (CORRECTO)
window.Echo = new Echo({
  broadcaster: 'pusher',
  key: import.meta.env.VITE_PUSHER_APP_KEY,
  cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
  forceTLS: true,
})

console.log('🟢 Echo inicializado con Pusher')

// 🚀 Vue App
createApp(DashboardLayout).mount('#app')
