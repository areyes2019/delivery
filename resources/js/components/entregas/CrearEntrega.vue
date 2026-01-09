<template>
  <form @submit.prevent="submit" class="crear-entrega-form">
    <!-- REMITENTE -->
    <div class="form-section">
      <h4>Remitente</h4>
      <input v-model="form.remitente_nombre" name="remitente_nombre" placeholder="Nombre" required />
      <input v-model="form.remitente_telefono" name="remitente_telefono" placeholder="Teléfono" />
    </div>

    <!-- DESTINATARIO -->
    <div class="form-section">
      <h4>Destinatario</h4>
      <input v-model="form.destinatario_nombre" name="destinatario_nombre" placeholder="Nombre" required />
      <input v-model="form.destinatario_telefono" name="destinatario_telefono" placeholder="Teléfono" />
    </div>

    <!-- DIRECCIONES -->
    <div class="form-section">
      <h4>Direcciones</h4>
      <input ref="pickupInput" v-model="form.pickup_description" name="pickup_description" placeholder="Dirección de recogida" />
      <input ref="destinationInput" v-model="form.destination_description" name="destination_description" placeholder="Dirección de destino" required />
    </div>

    <!-- COSTO -->
    <div class="form-section">
      <h4>Costo</h4>
      <input type="number" v-model="form.fare_offered" name="fare_offered" placeholder="Costo del envío" />
    </div>

    <!-- OBSERVACIONES -->
    <div class="form-section">
      <h4>Observaciones</h4>
      <textarea v-model="form.observaciones" name="observaciones"></textarea>
    </div>

    <button type="submit" class="submit-btn" :disabled="loading">
      {{ loading ? 'Guardando…' : 'Guardar Entrega' }}
    </button>

    <p v-if="error" class="error">{{ error }}</p>
  </form>
</template>

<script>
import axios from 'axios'
import { uiStore } from '@/store/ui'
import { entregasStore } from '@/store/entregas'
import { waitForGoogleMaps } from '@/utils/googleMaps'

export default {
  data() {
    return {
      uiStore,
      entregasStore,
      loading: false,
      error: null,
      form: {
        remitente_nombre: '',
        remitente_telefono: '',
        destinatario_nombre: '',
        destinatario_telefono: '',
        pickup_description: '',
        pickupLat: '',
        pickupLng: '',
        destination_description: '',
        destLat: '',
        destLng: '',
        fare_offered: '',
        observaciones: ''
      }
    }
  },

  async mounted() {
    try {
      await waitForGoogleMaps()
      this.initPlaces()
    } catch (e) {
      console.error('❌ Google Maps no cargó a tiempo', e)
    }
  },


  methods: {
    initPlaces() {
      const pickupAutocomplete = new google.maps.places.Autocomplete(this.$refs.pickupInput)
      pickupAutocomplete.addListener('place_changed', () => {
        const place = pickupAutocomplete.getPlace()
        if (!place.geometry) return
        this.form.pickup_description = place.formatted_address
        this.form.pickupLat = place.geometry.location.lat()
        this.form.pickupLng = place.geometry.location.lng()
      })

      const destinationAutocomplete = new google.maps.places.Autocomplete(this.$refs.destinationInput)
      destinationAutocomplete.addListener('place_changed', () => {
        const place = destinationAutocomplete.getPlace()
        if (!place.geometry) return
        this.form.destination_description = place.formatted_address
        this.form.destLat = place.geometry.location.lat()
        this.form.destLng = place.geometry.location.lng()
      })
    },

    async submit() {
      this.loading = true
      this.error = null

      try {
        if (!this.form.pickupLat || !this.form.destLat) {
          alert('Selecciona las direcciones desde la lista de Google')
          return
        }

        const formData = new FormData()
        Object.entries(this.form).forEach(([key, value]) => {
          if (key === 'pickupLat') formData.append('pickup_position[lat]', value)
          else if (key === 'pickupLng') formData.append('pickup_position[lng]', value)
          else if (key === 'destLat') formData.append('destination_position[lat]', value)
          else if (key === 'destLng') formData.append('destination_position[lng]', value)
          else formData.append(key, value)
        })

        const res = await axios.post('/api/client-requests', formData, {
          headers: { 'Content-Type': 'multipart/form-data' }
        })

        this.entregasStore.add(res.data.data ?? res.data)
        this.uiStore.createPanelOpen = false
        Object.keys(this.form).forEach(k => (this.form[k] = ''))
      } catch (e) {
        this.error = 'Error al crear la entrega'
      } finally {
        this.loading = false
      }
    }
  }
}
</script>
