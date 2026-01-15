<template>
  <div class="dashboard-layout">
    <Navbar
      @nuevo-envio="abrirFormulario"
      @logout="cerrarSesion"
    />

    <div class="container-fluid m-0 p-0">
      <div class="row m-0 p-0">

        <!-- DESPACHADOR -->
        <SidebarForm
          :open="sidebarOpen"
          :pendientes="pendientes"
          :enRuta="enRuta"
          @close="sidebarOpen = false"
          @entrega-creada="onEntregaCreada"
        />
        <MapView />
        <DriverState/> 
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios'

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

  /* -----------------------------------------
   * DATA
   * -----------------------------------------*/
  data () {
    return {
      // UI
      sidebarOpen: false,

      // Backend
      solicitudes: [],
      cargandoSolicitudes: false,

      // Driver
      entregaActiva: null
    }
  },

  /* -----------------------------------------
   * MOUNTED
   * -----------------------------------------*/
  mounted () {
    this.fetchSolicitudes()
  },

  /* -----------------------------------------
   * COMPUTED
   * -----------------------------------------*/
  computed: {
    pendientes () {
      return this.solicitudes.filter(
        s => s.status === 'CREATED'
      )
    },

    enRuta () {
      return this.solicitudes.filter(
        s => ['ACCEPTED', 'PICKED_UP', 'PAID'].includes(s.status)
      )
    }
  },

  /* -----------------------------------------
   * METHODS
   * -----------------------------------------*/
  methods: {
    abrirFormulario () {
      this.sidebarOpen = true
    },

    onEntregaCreada (nuevaEntrega) {
      // Agregar al inicio
      this.solicitudes.unshift(nuevaEntrega)

      // UX
      this.sidebarOpen = false
    },

    async fetchSolicitudes () {
      this.cargandoSolicitudes = true

      try {
        const res = await axios.get('/client-requests')
        this.solicitudes = res.data
      } catch (e) {
        console.error('Error cargando solicitudes', e)
      } finally {
        this.cargandoSolicitudes = false
      }
    },

    /* ---------------------------
     * DRIVER ACTIONS (placeholder)
     * ---------------------------*/
    aceptarEntrega (id) {
      console.log('Aceptar entrega', id)
    },

    iniciarEntrega (id) {
      console.log('Iniciar entrega', id)
    },

    cobrarEntrega (id) {
      console.log('Cobrar entrega', id)
    },

    completarEntrega (id) {
      console.log('Completar entrega', id)
    },

    /* ---------------------------
     * AUTH
     * ---------------------------*/
    async cerrarSesion () {
      try {
        await axios.post('/logout')
      } catch (e) {
        console.warn('Error cerrando sesión', e)
      } finally {
        localStorage.removeItem('token')
        window.location.href = '/login'
      }
    }
  }
}
</script>
