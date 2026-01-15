<template>
  <H2>PANEL DEL DRIVER</H2>
    <section v-if="entregaActiva">
      <h3>Entrega en curso</h3>
      <p><strong>ID</strong>{{ entregaActiva.id }}</p>
      <p><strong>Status</strong>{{ entregaActiva.status }}</p>
      <button v-if="entregaActiva.status === 'ACCEPTED'" @click="iniciar">Iniciar entrega</button>
      <button v-if="entregaActiva.status === 'PICKED_UP'" @click="cobrar">Iniciar entrega</button>
      <button v-if="entregaActiva.status === 'PAID'" @click="entregar">Iniciar entrega</button>
      <MapView v-if="entregaActiva && entregaActiva.status === 'PICKED_UP'" />
    </section>
    <section v-else>
      <h3>Entregas disponibles</h3>
      <p v-if="entregas.length === 0">No hay entrgas disponibles</p>
      <ul>
        <li v-for="entrega in entregas">
          <p><strong>Entrega # {{ entrega.id }}</strong></p>
          <p>Status: {{ entrega.status }}</p>
          <button @click="aceptar(entrega.id)">Aceptar Entrega</button>
        </li>
      </ul>
    </section>
</template>


<script>
import axios from 'axios'
import MapView from '@/components/dashboard/MapView.vue'

export default {
  components:{
    MapView,
  },
  data() {
    return {
      entregas: [],
      entregaActiva: null,
    }
  },

  mounted() {
    this.cargarEstado()
  },

  methods: {
    async cargarEstado(){
      const activa = await axios.get('/driver/entrega-actual')
      this.entregaActiva = activa.data && activa.data.id ? activa.data:null
      if (!this.entregaActiva) {
        const res = await axios.get('/driver/tablero')
        this.entregas = res.data
      }
    },
    async iniciar() {
      await axios.post(
        `/driver/entregas/${this.entregaActiva.id}/iniciar`
      )
      this.cargarEstado()
    },
    async cobrar(){
      await axios.post('/driver/entregas/${this.entregaActiva.id}/cobrar')
      this.cargarEstado()   
    },
    
    async aceptar(id) {
      await axios.post(`/driver/entregas/${id}/aceptar`)
      this.cargarEstado()
    },

    async entregar() {
      await axios.post(
        `/driver/entregas/${this.entregaActiva.id}/entregar`
      )
      this.cargarEstado()
    },
  },
}
</script>

<style scoped>
.driver-view {
  padding: 16px;
}

button {
  margin-top: 8px;
}
</style>
