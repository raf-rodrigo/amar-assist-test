<script setup>
defineProps({ meta: Object })
defineEmits(['change'])
</script>

<template>
  <div v-if="meta && meta.last_page > 1" class="mt-5 flex flex-wrap items-center justify-center gap-3 sm:justify-between">
    <button class="btn-secondary" :disabled="meta.current_page === 1" @click="$emit('change', meta.current_page - 1)">Anterior</button>
    <span>Página {{ meta.current_page }} de {{ meta.last_page }}</span>
    <button class="btn-secondary" :disabled="meta.current_page === meta.last_page" @click="$emit('change', meta.current_page + 1)">Próxima</button>
  </div>
</template>
