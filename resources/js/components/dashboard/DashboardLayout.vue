<template>
  <div class="dashboard">
    <Navbar />

    <div class="dashboard-body">
      <SidebarEntregas />
      <CreatePanel />
      <MapView />
    </div>
  </div>
</template>

<script>
import Navbar from './Navbar.vue'
import SidebarEntregas from './SidebarEntregas.vue'
import CreatePanel from './CreatePanel.vue'
import MapView from './MapView.vue'

export default {
  components: {
    Navbar,
    SidebarEntregas,
    CreatePanel,
    MapView
  }
}
</script>

<style>
.dashboard-body {
  display: flex;
  height: calc(100vh - 56px);
}
</style>
