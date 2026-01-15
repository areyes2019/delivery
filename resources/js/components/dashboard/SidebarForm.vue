<template>
  <!-- PANEL IZQUIERDO -->
  <div class="col-md-2 p-0">
    <div class="panel">

      <!-- 🔹 TABS -->
      <ul class="nav nav-tabs nav-fill">
        <li class="nav-item">
          <button
            type="button"
            class="nav-link"
            :class="{ active: activeTab === 'pendientes' }"
            @click="activeTab = 'pendientes'"
          >
            Pendientes
          </button>
        </li>

        <li class="nav-item">
          <button
            type="button"
            class="nav-link"
            :class="{ active: activeTab === 'ruta' }"
            @click="activeTab = 'ruta'"
          >
            En ruta
          </button>
        </li>
      </ul>

      <!-- 🔹 LISTADO -->
      <div class="p-2">

        <!-- 🟡 PENDIENTES -->
        <div v-show="activeTab === 'pendientes'">
          <a
            v-for="item in pendientes"
            :key="item.id"
            href="#"
            class="text-decoration-none"
          >
            <div class="card mb-2 rounded-0">
              <div class="card-body">
                <p class="m-0"><strong>Cliente</strong></p>
                <p class="m-0">{{ item.destinatario_nombre }}</p>

                <p class="m-0"><strong>Dirección</strong></p>
                <p>{{ shortAddress(item.pickup_description) }}</p>

                <span class="badge bg-warning text-dark">
                  {{ statusLabel(item.status) }}
                </span>
              </div>
            </div>
          </a>

          <div
            v-if="!pendientes.length"
            class="text-muted text-center py-3"
          >
            No hay solicitudes pendientes
          </div>
        </div>

        <!-- 🔵 EN RUTA -->
        <div v-show="activeTab === 'ruta'">
          <a
            v-for="item in enRuta"
            :key="item.id"
            href="#"
            class="text-decoration-none"
          >
            <div class="card mb-2 rounded-0">
              <div class="card-body">
                <p class="m-0">Cliente</p>
                <p>{{ item.destinatario_nombre }}</p>

                <h6>Dirección</h6>
                <p>{{ shortAddress(item.pickup_description) }}</p>

                <span class="badge bg-primary">
                  {{ statusLabel(item.status) }}
                </span>
              </div>
            </div>
          </a>

          <div
            v-if="!enRuta.length"
            class="text-muted text-center py-3"
          >
            No hay entregas en ruta
          </div>
        </div>

      </div>
    </div>
  </div>
  <!-- FIN LISTADO -->

  <!-- FORMULARIO (COMPONENTE REAL) -->
  <CrearEntrega
    :open="open"
    @close="$emit('close')"
    @entrega-creada="$emit('entrega-creada', $event)"
  />
</template>

<script>
import CrearEntrega from './CrearEntrega.vue'

const STATUS = {
  CREATED: 'CREATED',
  ACCEPTED: 'ACCEPTED',
  PICKED_UP: 'PICKED_UP',
  PAID: 'PAID',
  DELIVERED: 'DELIVERED',
  CANCELED: 'CANCELED'
}

export default {
  name: 'SidebarForm',

  components: { CrearEntrega },

  props: {
    open: {
      type: Boolean,
      default: false
    },
    pendientes: {
      type: Array,
      default: () => []
    },
    enRuta: {
      type: Array,
      default: () => []
    }
  },

  emits: ['close', 'entrega-creada'],

  data() {
    return {
      activeTab: 'pendientes'
    }
  },

  methods: {
    statusLabel(status) {
      switch (status) {
        case STATUS.CREATED:
          return 'Pendiente'
        case STATUS.ACCEPTED:
          return 'Asignada'
        case STATUS.PICKED_UP:
          return 'En ruta'
        case STATUS.PAID:
          return 'Pagada'
        case STATUS.DELIVERED:
          return 'Entregada'
        case STATUS.CANCELED:
          return 'Cancelada'
        default:
          return status
      }
    },
    shortAddress(address) {
      if (!address) return ''

      // divide por comas
      const parts = address.split(',')

      // calle + colonia (máx 2 partes)
      return parts.slice(0, 2).join(',').trim()
    },

  }
}
</script>
