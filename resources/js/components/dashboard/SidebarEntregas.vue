<template>
  <aside ref="sidebar" class="sidebar">
    <EntregaItem
      v-for="entrega in entregasStore.items"
      :key="entrega.id"
      :entrega="entrega"
    />
  </aside>
</template>

<script>
import { ref, watch, nextTick, onMounted } from 'vue'
import { entregasStore } from '@/store/entregas'
import EntregaItem from '@/components/entregas/EntregaItem.vue'

export default {
  components: { EntregaItem },

  setup() {
    const sidebar = ref(null)

    // 🔥 ESTO FALTABA
    onMounted(() => {
      if (entregasStore.items.length === 0) {
        entregasStore.fetch()
      }
    })

    watch(
      () => entregasStore.items.length,
      async () => {
        await nextTick()
        if (sidebar.value) {
          sidebar.value.scrollTop = sidebar.value.scrollHeight
        }
      }
    )

    return {
      entregasStore,
      sidebar
    }
  }
}
</script>
