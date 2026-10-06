<script setup>
import { onBeforeUnmount, onMounted } from 'vue'

const props = defineProps({
  title: { type: String, default: 'Confirmar ação' },
  message: { type: String, required: true },
  confirmText: { type: String, default: 'Confirmar' },
  cancelText: { type: String, default: 'Cancelar' },
  loading: { type: Boolean, default: false },
  danger: { type: Boolean, default: false },
})
const emit = defineEmits(['confirm', 'cancel'])

function cancel() {
  if (!props.loading) emit('cancel')
}

function handleKeydown(event) {
  if (event.key === 'Escape') cancel()
}

onMounted(() => {
  document.body.classList.add('overflow-hidden')
  window.addEventListener('keydown', handleKeydown)
})

onBeforeUnmount(() => {
  document.body.classList.remove('overflow-hidden')
  window.removeEventListener('keydown', handleKeydown)
})
</script>

<template>
  <div
    class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/60 p-4"
    role="dialog"
    aria-modal="true"
    aria-labelledby="confirmation-title"
    @click.self="cancel"
  >
    <section class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl">
      <h2 id="confirmation-title" class="text-xl font-bold text-slate-900">{{ title }}</h2>
      <p class="mt-3 text-slate-600">{{ message }}</p>

      <div class="mt-6 flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">
        <button class="btn-secondary" type="button" :disabled="loading" @click="cancel">
          {{ cancelText }}
        </button>
        <button
          :class="danger ? 'btn-danger' : 'btn'"
          type="button"
          :disabled="loading"
          @click="emit('confirm')"
        >
          {{ loading ? 'Aguarde...' : confirmText }}
        </button>
      </div>
    </section>
  </div>
</template>
