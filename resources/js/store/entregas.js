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
      const res = await axios.get('client-requests')

      this.items = (res.data.data ?? res.data).map(e => ({
        id: e.id,
        estado: e.status,
        destinatario_nombre: e.destinatario_nombre ?? e.destinatario,
        remitente_nombre: e.remitente_nombre,
        pickup_description: e.pickup_description,
        destination_description: e.destination_description,
        pickup_position: e.pickup_position,
        destination_position: e.destination_position,
        created_at: e.created_at
      }))
      .sort((a, b) => new Date(a.created_at) - new Date(b.created_at))

    } catch (e) {
      this.error = 'Error al cargar envíos'
    } finally {
      this.loading = false
    }
  },

  select(entrega) {
    console.log('📦 Entrega seleccionada:', entrega)
    this.selected = entrega
  }
})
