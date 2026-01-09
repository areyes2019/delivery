<template>
  <div class="entrega-item">
    <div class="top">
      <strong>{{ entrega.cliente }}</strong>
      <span class="estado" :class="entrega.estado">
        {{ entrega.estado }}
      </span>
    </div>

    <small>{{ entrega.direccion }}</small>
  </div>
</template>

<script>
export default {
  props: {
    entrega: {
      type: Object,
      required: true
    }
  }
}
</script>
