<template>
  <div class="delivery-form col-md-4" :class="{ active: open }">

    <!-- 🔹 HEADER -->
    <div class="delivery-header d-flex justify-content-between align-items-center mb-3">
      <h5 class="mb-0">Nuevo Envío</h5>

      <button
        type="button"
        class="btn btn-sm btn-light"
        @click="$emit('close')"
        aria-label="Cerrar"
      >
        ✕
      </button>
    </div>

    <!-- 🔹 FORM -->
    <form @submit.prevent="submit" class="crear-entrega-form">

      <div class="form-section">
        <h4>Envía</h4>
        <input
          class="form-control"
          v-model="form.remitente_nombre"
          placeholder="Nombre de quien envía"
          required
        />
        <input
          class="form-control"
          v-model="form.remitente_telefono"
          placeholder="Teléfono"
        />
      </div>

      <div class="form-section">
        <h4>Recibe</h4>
        <input
          class="form-control"
          v-model="form.destinatario_nombre"
          placeholder="Nombre de quien recibe"
          required
        />
        <input
          class="form-control"
          v-model="form.destinatario_telefono"
          placeholder="Teléfono"
        />
      </div>

      <div class="form-section">
        <h4>Direcciones</h4>
        <input
          ref="pickupInput"
          class="form-control"
          v-model="form.pickup_description"
          placeholder="Dirección de recogida"
        />
        <input
          ref="destinationInput"
          class="form-control"
          v-model="form.destination_description"
          placeholder="Dirección de destino"
          required
        />
      </div>

      <div class="form-section">
        <h4>Costo</h4>
        <input
          class="form-control"
          type="text"
          v-model="form.fare_offered"
          placeholder="Costo del envío"
        />
      </div>

      <div class="form-section">
        <h4>Observaciones</h4>
        <textarea
          class="form-control"
          v-model="form.observaciones"
        ></textarea>
      </div>

      <button
        type="submit"
        class="btn btn-dark rounded-pill w-100"
        :disabled="loading"
      >
        {{ loading ? 'Guardando…' : 'Guardar Entrega' }}
      </button>

      <p v-if="error" class="text-danger mt-2">{{ error }}</p>
    </form>
  </div>
</template>

<script>
import axios from 'axios'
import { waitForGoogleMaps } from '@/utils/googleMaps'

export default {
  props: {
    open: Boolean
  },

  emits: ['entrega-creada', 'close'],

  data() {
    return {
      loading: false,
      error: null,
      form: this.emptyForm()
    }
  },

  async mounted() {
    await waitForGoogleMaps()
    this.initPlaces()
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
      // 🔒 Validación mínima antes de enviar
      if (
        !this.form.pickupLat ||
        !this.form.pickupLng ||
        !this.form.destLat ||
        !this.form.destLng
      ) {
        this.error = 'Selecciona direcciones válidas desde el autocompletado'
        return
      }

      this.loading = true
      this.error = null

      try {
        // ✅ PAYLOAD CORRECTO PARA LARAVEL
        const payload = {
          remitente_nombre: this.form.remitente_nombre,
          remitente_telefono: this.form.remitente_telefono,
          destinatario_nombre: this.form.destinatario_nombre,
          destinatario_telefono: this.form.destinatario_telefono,

          pickup_description: this.form.pickup_description,
          destination_description: this.form.destination_description,

          pickup_position: {
            lat: this.form.pickupLat,
            lng: this.form.pickupLng
          },

          destination_position: {
            lat: this.form.destLat,
            lng: this.form.destLng
          },

          fare_offered: this.form.fare_offered,
          observaciones: this.form.observaciones
        }

        const res = await axios.post('/client-requests', payload, {
          baseURL: '/'
        })

        this.$emit('entrega-creada', res.data.data)
        this.$emit('close')
        this.form = this.emptyForm()
      } catch (e) {
        this.error = 'Error al crear la entrega'
      } finally {
        this.loading = false
      }
    }
  },
  guardarRemitente() {
    // Validación defensiva
    if (!this.form.remitente_nombre) return

    const key = 'remitentes_recientes'
    const existentes = JSON.parse(localStorage.getItem(key) || '[]')

    const nuevo = {
      nombre: this.form.remitente_nombre,
      telefono: this.form.remitente_telefono
    }

    // Evitar duplicados por nombre + teléfono
    const filtrados = existentes.filter(
      r => r.nombre !== nuevo.nombre || r.telefono !== nuevo.telefono
    )

    filtrados.unshift(nuevo)

    // Limitar a últimos 5
    localStorage.setItem(key, JSON.stringify(filtrados.slice(0, 5)))
  }


}
</script>
