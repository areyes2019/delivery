<template>
  <!-- PANEL DERECHO -->
  <div class="col-md-2 p-0">
    <div class="panel">

      <div
        v-for="item in envios"
        :key="item.id"
        class="card"
      >
        <a
          href="#"
          class="text-decoration-none text-dark"
          data-bs-toggle="modal"
          data-bs-target="#modalEntrega"
          @click.prevent="abrirModal(item)"
        >
          <div class="card-body">

            <!-- Repartidor -->
            <div class="d-flex align-items-center mb-3">
              <img
                src="https://i.pravatar.cc/80?img=12"
                class="rounded-circle me-3"
                width="50"
                height="50"
              >
              <div>
                <strong>Repartidor</strong><br>
                <strong>{{ item.driver?.name || 'Sin Asignar' }}</strong>
              </div>
            </div>

            <strong>Recibe:</strong>
            <p class="mb-0">{{ item.destinatario_nombre }}</p>

            <strong>Dirección</strong>
            <p class="mb-2">{{ item.pickup_description }}</p>

            <span class="badge bg-success mb-2">
              {{ statusLabel(item.status) }}
            </span>

            <!-- PROGRESO -->
            <div class="progress" style="height:6px;">
              <div
                class="progress-bar bg-success"
                :style="{ width: progressValue(item.status) + '%' }"
              ></div>
            </div>

            <small class="text-success mt-2 d-block">
              Progreso
            </small>

          </div>
        </a>
      </div>

    </div>
  </div>

  <!-- MODAL DETALLE DE ENVÍO -->
  <div class="modal fade" id="modalEntrega" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content rounded-0">

        <div class="modal-header">
          <h5 class="modal-title">Seguimiento de Entrega</h5>
          <button class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body" v-if="envioSeleccionado">

          <!-- TIMELINE -->
          <ul class="timeline">
            <li
              v-for="(step, index) in timeline(envioSeleccionado)"
              :key="index"
            >
              <span class="time">{{ formatTime(step.time) }}</span>
              <span class="dot bg-success"></span>
              <span class="text">{{ step.label }}</span>
            </li>
          </ul>

          <hr>

          <div class="text-center">
            <strong>Estado actual:</strong>
            {{ statusLabel(envioSeleccionado.status) }}
          </div>

        </div>

      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'DriverState',

  props: {
    envios: {
      type: Array,
      default: () => []
    }
  },

  data() {
    return {
      envioSeleccionado: null
    }
  },

  methods: {
    abrirModal(item) {
      this.envioSeleccionado = item
    },

    statusLabel(status) {
      switch (status) {
        case 'ACCEPTED':
          return 'Asignado'
        case 'PICKED_UP':
          return 'En ruta'
        case 'PAID':
          return 'Pagado'
        case 'DELIVERED':
          return 'Entregado'
        default:
          return status
      }
    },

    progressValue(status) {
      switch (status) {
        case 'ACCEPTED':
          return 25
        case 'PICKED_UP':
          return 50
        case 'PAID':
          return 75
        case 'DELIVERED':
          return 100
        default:
          return 0
      }
    },

    timeline(item) {
      if (!item) return []

      const steps = []

      steps.push({
        label: 'Solicitado',
        time: item.created_at
      })

      if (item.started_at) {
        steps.push({
          label: 'En ruta',
          time: item.started_at
        })
      }

      if (item.paid_at) {
        steps.push({
          label: 'Pagado',
          time: item.paid_at
        })
      }

      if (item.delivered_at) {
        steps.push({
          label: 'Entregado',
          time: item.delivered_at
        })
      }

      return steps
    },

    formatTime(date) {
      if (!date) return ''
      return new Date(date).toLocaleTimeString([], {
        hour: '2-digit',
        minute: '2-digit'
      })
    }
  }
}
</script>
