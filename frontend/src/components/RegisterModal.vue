<script setup>
import { onBeforeUnmount, onMounted, reactive, ref } from 'vue'
import { useAuthStore } from '../stores/auth'

const emit = defineEmits(['close'])
const auth = useAuthStore()
const loading = ref(false)
const errors = ref({})
const form = reactive({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
})

function handleKeydown(event) {
  if (event.key === 'Escape' && !loading.value) emit('close')
}

async function submit() {
  loading.value = true
  errors.value = {}

  try {
    await auth.register(form)
  } catch (exception) {
    errors.value = exception.response?.data?.errors || {
      general: [exception.response?.data?.message || 'Não foi possível criar a conta.'],
    }
  } finally {
    loading.value = false
  }
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
    aria-labelledby="register-title"
    @click.self="!loading && emit('close')"
  >
    <section class="max-h-[90vh] w-full max-w-md overflow-y-auto rounded-2xl bg-white p-6 shadow-2xl sm:p-8">
      <div class="mb-5 flex items-start justify-between gap-4">
        <div>
          <h2 id="register-title" class="text-2xl font-bold">Criar cadastro</h2>
          <p class="mt-1 text-sm text-slate-500">Preencha seus dados para começar.</p>
        </div>
        <button
          class="rounded-lg px-3 py-1.5 text-xl text-slate-500 hover:bg-slate-100 hover:text-slate-800"
          type="button"
          aria-label="Fechar cadastro"
          :disabled="loading"
          @click="emit('close')"
        >
          ×
        </button>
      </div>

      <form class="space-y-4" @submit.prevent="submit">
        <div>
          <label class="label">Nome</label>
          <input v-model="form.name" class="input" type="text" maxlength="191" autocomplete="name" autofocus required>
          <small class="text-rose-600">{{ errors.name?.[0] }}</small>
        </div>
        <div>
          <label class="label">E-mail</label>
          <input v-model="form.email" class="input" type="email" maxlength="191" autocomplete="email" required>
          <small class="text-rose-600">{{ errors.email?.[0] }}</small>
        </div>
        <div>
          <label class="label">Senha</label>
          <input v-model="form.password" class="input" type="password" minlength="8" autocomplete="new-password" required>
          <small class="text-rose-600">{{ errors.password?.[0] }}</small>
        </div>
        <div>
          <label class="label">Confirmar senha</label>
          <input v-model="form.password_confirmation" class="input" type="password" minlength="8" autocomplete="new-password" required>
        </div>
        <p v-if="errors.general" class="text-sm text-rose-600">{{ errors.general[0] }}</p>
        <button class="btn w-full" :disabled="loading">
          {{ loading ? 'Criando conta...' : 'Criar conta' }}
        </button>
      </form>
    </section>
  </div>
</template>
