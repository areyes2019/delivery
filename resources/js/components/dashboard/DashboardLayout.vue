<template>
  <div class="dashboard-layout">
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
      <div class="row m-0 p-0">
        <!-- DESPACHADOR -->
        <SidebarForm
          :open="sidebarOpen"
          :solicitudes="colaSolicitudes"
          @close="sidebarOpen = false"
          @entrega-creada="onEntregaCreada"
        />

        <MapView />

        <DriverState
          :envios="solicitudesEnProceso"
        />
      </div>
    </div>
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
      channel: null
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
    abrirFormulario () {
      this.sidebarOpen = true
    },

    async onEntregaCreada () {
      await this.fetchSolicitudes()
      //this.sidebarOpen = false
      this.onSolicitudActualizada(data)
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

    setupWebSocket () {
      if (!window.Echo) {
        console.error('Echo no disponible')
        return
      }

      console.log('🔌 Conectando a Reverb…')

      this.channel = window.Echo.private('dashboard')

      this.channel
        .listen('.client-request.accepted', data => {
          console.log('📡 ACCEPTED', data)

          const existente = this.solicitudes.find(s => s.id === data.id)

          // 🔔 solo alertar si antes NO estaba aceptada
          if (!existente || existente.status !== 'ACCEPTED') {
            this.mostrarAlerta(
              `🚴 ${data.driver?.name ?? 'Un repartidor'} tomó la solicitud #${data.id}`
            )
          }

          this.onSolicitudActualizada(data)
        })
        

        .listen('.client-request.picked-up', data => {
          console.log('📡 PICKED_UP', data)
          this.onSolicitudActualizada(data)
        })
        .listen('.client-request.paid', data => {
          console.log('📡 PAID', data)
          this.onSolicitudActualizada(data)
        })
        .listen('.client-request.completed', data => {
          console.log('📡 COMPLETED', data)
          this.onSolicitudCompletada(data)
        })

      console.log('🟢 Suscrito al canal dashboard')
    },

    onSolicitudActualizada (data) {
      const index = this.solicitudes.findIndex(
        s => s.id === data.id
      )

      if (index !== -1) {
        // actualizar existente
        this.solicitudes[index] = {
          ...this.solicitudes[index],
          ...data
        }
      } else {
        // 👇 insertar la solicitud si no existe
        this.solicitudes.unshift({
          ...data
        })
      }
    },


    onSolicitudCompletada (data) {
      this.solicitudes = this.solicitudes.filter(
        s => s.id !== data.id
      )
    },

    mostrarAlerta (mensaje, duracion = 5000) {
      this.alerta = mensaje
      setTimeout(() => {
        this.alerta = null
      }, duracion)
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

<style scoped>
.alert-success {
  background-color: #d4edda;
  border-color: #c3e6cb;
  color: #155724;
}
</style>
