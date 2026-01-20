<template>
  <!-- PANEL IZQUIERDO -->
  <div class="col-md-2 p-0">
    <div class="panel">

      <!-- 🔹 HEADER -->
      <div class="p-2 border-bottom">
        <h6 class="m-0 text-center fw-bold">
          Cola de solicitudes
        </h6>
      </div>

      <!-- 🔹 LISTADO -->
      <div class="p-2">

        <a
          v-for="item in solicitudes"
          :key="item.id"
          href="#"
          class="text-decoration-none"
        >
          <div class="card mb-2 rounded-0">
            <div class="card-body">
              <p class="m-0"><strong>Cliente</strong></p>
              <p class="m-0">{{ item.destinatario_nombre }}</p>

              <p class="m-0"><strong>Dirección</strong></p>
              <p class="m-0">{{ shortAddress(item.pickup_description) }}</p>

              <span class="badge bg-warning text-dark">
                Pendiente
              </span>
            </div>
          </div>
        </a>

        <div
          v-if="!solicitudes.length"
          class="text-muted text-center py-3"
        >
          No hay solicitudes en cola
        </div>

      </div>
    </div>
  </div>

  <!-- FORMULARIO -->
  <CrearEntrega
    :open="open"
    @close="$emit('close')"
    @entrega-creada="$emit('entrega-creada', $event)"
  />
</template>
<script>
import CrearEntrega from './CrearEntrega.vue'

export default {
  name: 'SidebarForm',

  components: { CrearEntrega },

  props: {
    open: Boolean,
    solicitudes: {
      type: Array,
      default: () => []
    }
  },

  emits: ['close', 'entrega-creada'],

  methods: {
    shortAddress(address) {
      if (!address) return ''
      return address.split(',').slice(0, 2).join(',').trim()
    }
  }
}
</script>
