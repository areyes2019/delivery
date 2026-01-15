<template>
  <div class="col-md-8 mb-0 p-0">
    <div class="panel-map">
      <div ref="mapEl" class="map"></div>
    </div>
  </div>
</template>

<script>
//IMPORTACION DEL ARCHIVO GOOGLEMAPS
import { waitForGoogleMaps } from '@/utils/googleMaps'

export default {
  name: 'MapView',

  props: {
    entrega: {
      type: Object,
      default: null
    },
    driverPosition: {
      type: Object,
      default: null
    }
  },

  data() {
    return {
      map: null,
      directionsService: null,
      directionsRenderer: null,
    }
  },

  mounted() {
    this.init()
  },

  methods: {
    async init() {
      try {
        await waitForGoogleMaps()
        this.initMap()
      } catch (e) {
        console.error('Error cargando Google Maps:', e)
      }
    },
    //AQUI INICIAMOS EL MAPA DE GOOGLE
    initMap() {
      const center = this.entrega?.pickup_position
        ? {
            lat: Number(this.entrega.pickup_position.lat),
            lng: Number(this.entrega.pickup_position.lng),
          }
        : this.getFallbackLocation()

      this.map = new google.maps.Map(this.$refs.mapEl, {
        center,
        zoom: 13,
      })

      this.directionsService = new google.maps.DirectionsService()
      this.directionsRenderer = new google.maps.DirectionsRenderer()
      this.directionsRenderer.setMap(this.map)

      setTimeout(() => {
        google.maps.event.trigger(this.map, 'resize')
      }, 200)

      if (this.entrega && this.driverPosition) {
        this.drawRoute()
      }
    },

    getFallbackLocation() {
      return { lat: 20.5279, lng: -100.8123 } // Celaya
    },
    drawRoute() {
      if (!this.map) return
      if (!this.entrega?.pickup_position) return
      if (!this.driverPosition) return

      const origin = {
        lat: Number(this.driverPosition.lat),
        lng: Number(this.driverPosition.lng),
      }

      const destination = {
        lat: Number(this.entrega.pickup_position.lat),
        lng: Number(this.entrega.pickup_position.lng),
      }

      if (
        isNaN(origin.lat) || isNaN(origin.lng) ||
        isNaN(destination.lat) || isNaN(destination.lng)
      ) return

      this.directionsService.route(
        {
          origin,
          destination,
          travelMode: google.maps.TravelMode.DRIVING,
        },
        (result, status) => {
          if (status === 'OK') {
            this.directionsRenderer.setDirections(result)
          }
        }
      )
    },

  },
  watch: {
    entrega: {
      deep: true,
      handler() {
        if (!this.map) return
        this.drawRoute()
      }
    },

    driverPosition: {
      deep: true,
      handler() {
        if (!this.map) return
        this.drawRoute()
      }
    }
  }
}
</script>

<style scoped>
.panel-map {
  position: relative;
  width: 100%;
  height: 100%;
}

.map {
  position: absolute;
  inset: 0;
}
</style>
