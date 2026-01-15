import '../css/dashboard.css'
import axios from 'axios'
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

// CSRF token
const token = document.head.querySelector('meta[name="csrf-token"]')
if (token) {
  axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content
}

// Interceptor global
axios.interceptors.response.use(
  response => response,
  error => {
    if (error.response?.status === 401) {
      console.warn('⚠️ No autenticado (401)')
    }
    return Promise.reject(error)
  }
)

// 🚀 UNA SOLA APP, ID NORMAL
createApp(DashboardLayout).mount('#app')
