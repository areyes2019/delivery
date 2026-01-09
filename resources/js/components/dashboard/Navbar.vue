<template>
  <div class="navbar">
    <div>🚚 Delivery Celaya</div>

    <div class="right">
      <button class="btn-create" @click="uiStore.createPanelOpen = true">
        ➕ Crear Entrega
      </button>

      <div class="user">{{ userName }}</div>

      <form method="POST" action="/logout">
        <input type="hidden" name="_token" :value="csrf">
        <button type="submit">Salir</button>
      </form>
    </div>
  </div>
</template>

<script>
import { uiStore } from '@/store/ui'

export default {
  data() {
    return {
      uiStore,
      userName: window.AUTH_USER?.name ?? 'Usuario',
      csrf: document.querySelector('meta[name="csrf-token"]').content
    }
  }
}
</script>
