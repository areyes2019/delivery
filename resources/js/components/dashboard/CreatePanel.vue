<template>
  <aside class="create-panel" :class="{ active: uiStore.createPanelOpen }">
    <div class="create-header">
      <h3>➕ Crear Entrega</h3>
      <button type="button" @click="uiStore.createPanelOpen = false">✕</button>
    </div>

    <!-- 👇 reenviamos el evento al padre -->
    <CrearEntrega @entrega-creada="onEntregaCreada" />
  </aside>
</template>

<script>
import { uiStore } from '@/store/ui'
import CrearEntrega from '../entregas/CrearEntrega.vue'

export default {
  components: { CrearEntrega },

  // 🔥 ESTO ES LO QUE FALTABA
  emits: ['entrega-creada'],

  data() {
    return { uiStore }
  },

  methods: {
    onEntregaCreada(entrega) {
      // reenviar al DashboardLayout
      this.$emit('entrega-creada', entrega)
    }
  }
}
</script>
