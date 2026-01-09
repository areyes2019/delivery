<template>
  <main class="map-wrapper">
    <div ref="mapEl" class="map"></div>
  </main>
</template>

<script>
import { entregasStore } from '@/store/entregas'

export default {
  data() {
    return {
      map: null,
      checkInterval: null,
      entregasStore
    }
  },

  mounted() {
    this.waitForGoogleMaps()
  },

  beforeUnmount() {
    if (this.checkInterval) {
      clearInterval(this.checkInterval)
    }
  },

  methods: {
    waitForGoogleMaps() {
      this.checkInterval = setInterval(() => {
        if (window.google && window.google.maps) {
          clearInterval(this.checkInterval)
          this.initMap()
        }
      }, 100)
    },

    initMap() {
      this.map = new google.maps.Map(this.$refs.mapEl, {
        center: { lat: 20.5931, lng: -100.3925 }, // Celaya
        zoom: 13
      })

      // 🔥 Forzar redibujado
      setTimeout(() => {
        google.maps.event.trigger(this.map, 'resize')

        // 🔥 SI YA HAY ENTREGA SELECCIONADA → CENTRAR
        if (this.entregasStore.selected) {
          this.centerOnEntrega(this.entregasStore.selected)
        }
      }, 200)
    },

    centerOnEntrega(entrega) {
      if (!entrega?.pickup_position) return

      const lat = Number(entrega.pickup_position.lat)
      const lng = Number(entrega.pickup_position.lng)

      if (isNaN(lat) || isNaN(lng)) return

      this.map.panTo({ lat, lng })
      this.map.setZoom(15)
    }
  },

  watch: {
    // 👂 cuando cambie la entrega seleccionada
    'entregasStore.selected'(entrega) {
      if (!this.map || !entrega) return
      this.centerOnEntrega(entrega)
    }
  }
}
</script>

<style scoped>
.map-wrapper {
  flex: 1;
  position: relative;
}

.map {
  position: absolute;
  inset: 0;
}
</style>
