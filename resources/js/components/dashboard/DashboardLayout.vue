<template>
  <div class="dashboard">
    <Navbar />

    <div class="dashboard-body">
      <SidebarEntregas />
      <CreatePanel @entrega-creada="onEntregaCreada" />
      <MapView />
    </div>
  </div>
</template>

<script>
import Navbar from './Navbar.vue'
import SidebarEntregas from './SidebarEntregas.vue'
import CreatePanel from './CreatePanel.vue'
import MapView from './MapView.vue'
import { entregasStore } from '@/store/entregas'

export default {
  components: {
    Navbar,
    SidebarEntregas,
    CreatePanel,
    MapView
  },

  methods: {
    onEntregaCreada(entrega) {
      // ✅ insertar sin repintar
      entregasStore.items.push(entrega)

      // opcional: seleccionar
      entregasStore.selected = entrega
    }
  }
}
</script>
