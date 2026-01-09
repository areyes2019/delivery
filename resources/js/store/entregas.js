import { reactive } from 'vue'
import axios from 'axios'

export const entregasStore = reactive({
  items: [],
  selected: null,
  loading: false,
  error: null,

  async fetch() {
    this.loading = true
    try {
      const res = await axios.get('/api/client-requests')
      this.items = res.data.data ?? res.data
    } finally {
      this.loading = false
    }
  },

  select(entrega) {
    console.log('📦 Entrega seleccionada:', entrega)
    this.selected = entrega
  }
})
