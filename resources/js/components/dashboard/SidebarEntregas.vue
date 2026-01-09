<template>
  <aside class="sidebar">
    <h3>Envíos</h3>

    <div v-if="entregasStore.loading">
      Cargando envíos…
    </div>

    <div v-else-if="entregasStore.error">
      {{ entregasStore.error }}
    </div>

    <div v-else>
      <div
        v-for="entrega in entregasStore.items"
        :key="entrega.id"
        class="card"
        @click="select(entrega)"
      >
        <strong>{{ entrega.destinatario_nombre }}</strong>
        <span>{{ entrega.destination_description }}</span>

        <span class="badge gray">
          {{ entrega.estado ?? 'Pendiente' }}
        </span>
      </div>
    </div>
  </aside>
</template>

<script>
import { entregasStore } from '@/store/entregas'

export default {
  name: 'SidebarEntregas',

  data() {
    return {
      entregasStore
    }
  },

  mounted() {
    this.entregasStore.fetch()
  },

  methods: {
    select(entrega) {
      this.entregasStore.select(entrega)
    }
  }
}
</script>

<style scoped>
.sidebar {
  width: 320px;
  background: #fff;
  border-right: 1px solid #e5e7eb;
  padding: 15px;
  overflow-y: auto;
}

.card {
  border: 1px solid #e5e7eb;
  border-radius: 6px;
  padding: 10px;
  margin-bottom: 10px;
  font-size: 13px;
  background: #f9fafb;
  cursor: pointer;
}

.card:hover {
  background: #eef2ff;
}
</style>
