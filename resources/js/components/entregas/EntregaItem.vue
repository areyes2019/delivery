<template>
  <div class="entrega-item">
    <div class="top">
      <strong class="cliente">
        {{ entrega.destinatario_nombre }}
      </strong>
    </div>

    <div class="direccion">
      {{ entrega.destination_description }}
    </div>

    <div class="estado" :class="entrega.estado">
      {{ entrega.estado }}
    </div>
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
