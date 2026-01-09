<template>
  <form @submit.prevent="submit" class="crear-entrega-form">
    <!-- REMITENTE -->
    <div class="form-section">
      <h4>Remitente</h4>
      <input v-model="form.remitente_nombre" placeholder="Nombre" required />
      <input v-model="form.remitente_telefono" placeholder="Teléfono" />
    </div>

    <!-- DESTINATARIO -->
    <div class="form-section">
      <h4>Destinatario</h4>
      <input v-model="form.destinatario_nombre" placeholder="Nombre" required />
      <input v-model="form.destinatario_telefono" placeholder="Teléfono" />
    </div>

    <!-- DIRECCIONES -->
    <div class="form-section">
      <h4>Direcciones</h4>

      <!-- 🔥 ESTE REF ES CRÍTICO -->
      <input
        ref="pickupInput"
        v-model="form.pickup_description"
        placeholder="Dirección de recogida"
      />

      <!-- 🔥 ESTE REF ES CRÍTICO -->
      <input
        ref="destinationInput"
        v-model="form.destination_description"
        placeholder="Dirección de destino"
        required
      />
    </div>

    <!-- COSTO -->
    <div class="form-section">
      <h4>Costo</h4>
      <input type="text" v-model="form.fare_offered" placeholder="Costo del envío" />
    </div>

    <!-- OBSERVACIONES -->
    <div class="form-section">
      <h4>Observaciones</h4>
      <textarea v-model="form.observaciones"></textarea>
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
import { waitForGoogleMaps } from '@/utils/googleMaps'

export default {
  emits: ['entrega-creada'],

  data() {
    return {
      uiStore,
      loading: false,
      error: null,
      form: this.emptyForm()
    }
  },

  async mounted() {
    try {
      await waitForGoogleMaps()
      this.initPlaces()
    } catch (e) {
      console.error('❌ Google Maps no cargó', e)
    }
  },

  methods: {
    emptyForm() {
      return {
        remitente_nombre: '',
        remitente_telefono: '',
        destinatario_nombre: '',
        destinatario_telefono: '',
        pickup_description: '',
        pickupLat: null,
        pickupLng: null,
        destination_description: '',
        destLat: null,
        destLng: null,
        fare_offered: null,
        observaciones: ''
      }
    },

    initPlaces() {
      if (!this.$refs.pickupInput || !this.$refs.destinationInput) {
        console.warn('⚠️ Refs de Google Places no listos')
        return
      }

      const pickup = new google.maps.places.Autocomplete(this.$refs.pickupInput)
      pickup.addListener('place_changed', () => {
        const place = pickup.getPlace()
        if (!place.geometry) return

        this.form.pickup_description = place.formatted_address
        this.form.pickupLat = place.geometry.location.lat()
        this.form.pickupLng = place.geometry.location.lng()
      })

      const destination = new google.maps.places.Autocomplete(this.$refs.destinationInput)
      destination.addListener('place_changed', () => {
        const place = destination.getPlace()
        if (!place.geometry) return

        this.form.destination_description = place.formatted_address
        this.form.destLat = place.geometry.location.lat()
        this.form.destLng = place.geometry.location.lng()
      })
    },


    async submit() {
      if (!this.form.pickupLat || !this.form.destLat) {
        this.error = 'Selecciona las direcciones desde la lista de Google'
        return
      }

      this.loading = true
      this.error = null

      try {
        await axios.get('/sanctum/csrf-cookie')

        const payload = {
          remitente_nombre: this.form.remitente_nombre,
          remitente_telefono: this.form.remitente_telefono,
          destinatario_nombre: this.form.destinatario_nombre,
          destinatario_telefono: this.form.destinatario_telefono,
          pickup_description: this.form.pickup_description,
          pickup_position: {
            lat: this.form.pickupLat,
            lng: this.form.pickupLng
          },
          destination_description: this.form.destination_description,
          destination_position: {
            lat: this.form.destLat,
            lng: this.form.destLng
          },
          fare_offered: this.form.fare_offered,
          observaciones: this.form.observaciones
        }

        const res = await axios.post('/api/client-requests', payload)

        // ✅ OBJETO NORMALIZADO PARA UI
        const nuevaEntrega = {
          id: res.data.data.id,
          estado: res.data.data.status,
          destinatario_nombre: this.form.destinatario_nombre,
          remitente_nombre: this.form.remitente_nombre,
          pickup_description: this.form.pickup_description,
          destination_description: this.form.destination_description,
          pickup_position: payload.pickup_position,
          destination_position: payload.destination_position,
          created_at: res.data.data.created_at
        }

        // 🚀 EMIT AL PADRE
        this.$emit('entrega-creada', nuevaEntrega)

        this.uiStore.createPanelOpen = false
        this.form = this.emptyForm()

      } catch (e) {
        this.error = e.response?.data?.message || 'Error al crear la entrega'
      } finally {
        this.loading = false
      }
    }
  }
}
</script>
