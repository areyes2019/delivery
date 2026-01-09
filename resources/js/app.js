import '../css/dashboard.css'
import './bootstrap'
import axios from 'axios'
import { createApp } from 'vue'

import DashboardLayout from './components/dashboard/DashboardLayout.vue'

// 🌐 Axios global
window.axios = axios

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

// 🚀 UNA SOLA APP VUE
const app = document.getElementById('dashboard-app')
if (app) {
  createApp(DashboardLayout).mount('#dashboard-app')
}
