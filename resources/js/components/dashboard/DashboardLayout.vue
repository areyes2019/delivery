<template>
  <!-- ⏳ Esperar a que el usuario exista -->
  <div v-if="user" class="dashboard-layout">
    <Navbar
      @nuevo-envio="abrirFormulario"
      @logout="cerrarSesion"
    />

    <!-- 🔔 Alerta -->
    <div
      v-if="alerta"
      class="alert alert-success alert-dismissible fade show position-fixed"
      style="top:70px; right:20px; z-index:2000; min-width:300px"
    >
      <i class="bi bi-bell-fill me-2"></i>
      {{ alerta }}
      <button type="button" class="btn-close" @click="alerta = null"></button>
    </div>

    <div class="container-fluid m-0 p-0">
      <div class="row m-0 p-0" style="min-height:100vh">
        <SidebarForm
          :open="sidebarOpen"
          :solicitudes="colaSolicitudes"
          @close="sidebarOpen = false"
          @entrega-creada="onEntregaCreada"
        />

        <MapView />

        <DriverState :envios="solicitudesEnProceso" />
      </div>
    </div>
  </div>

  <!-- ⌛ Fallback mientras carga -->
  <div
    v-else
    class="d-flex justify-content-center align-items-center"
    style="min-height:100vh"
  >
    <span class="text-muted">Cargando dashboard…</span>
  </div>
</template>

<script>
import web from '@/http/web'

import Navbar from './Navbar.vue'
import SidebarForm from './SidebarForm.vue'
import MapView from './MapView.vue'
import DriverState from './DriverState.vue'

export default {
  name: 'DashboardLayout',

  components: {
    Navbar,
    SidebarForm,
    MapView,
    DriverState
  },

  data () {
    return {
      sidebarOpen: false,
      alerta: null,
      solicitudes: [],
      cargandoSolicitudes: false,
      channel: null,
      user: null
    }
  },

  computed: {
    colaSolicitudes () {
      return this.solicitudes.filter(
        s => s.status?.toUpperCase() === 'CREATED'
      )
    },

    solicitudesEnProceso () {
      return this.solicitudes.filter(
        s => ['ACCEPTED', 'PICKED_UP', 'PAID']
          .includes(s.status?.toUpperCase())
      )
    }
  },

  async mounted () {
    await this.loadAuthUser()
    await this.fetchSolicitudes()
    this.setupWebSocket()
  },

  beforeUnmount () {
    if (this.channel) {
      this.channel.stopListening('.client-request.accepted')
      this.channel.stopListening('.client-request.picked-up')
      this.channel.stopListening('.client-request.paid')
      this.channel.stopListening('.client-request.completed')
    }
  },

  methods: {
    async loadAuthUser () {
      try {
        const res = await web.get('/me')
        this.user = res.data
        console.log('👤 Usuario cargado:', this.user)
      } catch (e) {
        console.error('❌ Error cargando usuario', e)
      }
    },

    setupWebSocket () {
      if (!window.Echo) {
        console.warn('Echo no disponible')
        return
      }

      if (!this.user || !this.user.cliente_id) {
        console.error('❌ Usuario o cliente_id no disponible', this.user)
        return
      }

      const clienteId = this.user.cliente_id

      console.log(`🔌 Suscrito a dashboard.cliente.${clienteId}`)

      this.channel = window.Echo
        .private(`dashboard.cliente.${clienteId}`)
        .listen('.client-request.accepted', data => {
          console.log('📡 ACCEPTED', data)
          this.onSolicitudActualizada(data)
        })
        .listen('.client-request.picked-up', data => {
          this.onSolicitudActualizada(data)
        })
        .listen('.client-request.paid', data => {
          this.onSolicitudActualizada(data)
        })
        .listen('.client-request.completed', data => {
          this.onSolicitudCompletada(data)
        })
    },

    abrirFormulario () {
      this.sidebarOpen = true
    },

    onEntregaCreada (data) {
      this.onSolicitudActualizada(data)
      this.sidebarOpen = false
    },

    async fetchSolicitudes () {
      this.cargandoSolicitudes = true
      try {
        const res = await web.get('/dashboard/client-requests')
        this.solicitudes = res.data
      } catch (e) {
        console.error(e)
        this.mostrarAlerta('Error al cargar las entregas')
      } finally {
        this.cargandoSolicitudes = false
      }
    },

    onSolicitudActualizada (data) {
      const index = this.solicitudes.findIndex(s => s.id === data.id)

      if (index !== -1) {
        this.solicitudes[index] = {
          ...this.solicitudes[index],
          ...data
        }
      } else {
        this.solicitudes.unshift(data)
      }
    },

    onSolicitudCompletada (data) {
      this.solicitudes = this.solicitudes.filter(
        s => s.id !== data.id
      )
    },

    mostrarAlerta (mensaje, duracion = 5000) {
      this.alerta = mensaje
      setTimeout(() => (this.alerta = null), duracion)
    },

    async cerrarSesion () {
      try {
        await web.post('/logout')
      } finally {
        window.location.href = '/login'
      }
    }
  }
}
</script>
