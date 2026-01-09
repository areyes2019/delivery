<template>
  <aside class="create-panel" :class="{ active: uiStore.createPanelOpen }">
    <div class="create-header">
      <h3>➕ Crear Entrega</h3>
      <button @click="uiStore.createPanelOpen = false">✕</button>
    </div>

    <div class="form-wrapper">
      <CrearEntrega />
    </div>
  </aside>
</template>

<script>
import { uiStore } from '@/store/ui'
import CrearEntrega from '../entregas/CrearEntrega.vue'

export default {
  components: { CrearEntrega },
  data() {
    return { uiStore }
  }
}
</script>
