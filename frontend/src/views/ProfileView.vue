<script setup>
import { onMounted, ref } from 'vue'
import { api } from '../api/client'
import { useAuthStore } from '../stores/auth'

const auth = useAuthStore()
const loading = ref(true)
const error = ref('')

onMounted(async () => {
  try {
    const { data } = await api.get('/user')
    auth.user = data
    localStorage.setItem('user', JSON.stringify(data))
  } catch (exception) {
    error.value = exception.response?.data?.message || 'Não foi possível carregar o perfil.'
  } finally {
    loading.value = false
  }
})
</script>

<template>
  <section class="mx-auto w-full max-w-2xl">
    <h1 class="mb-6 text-3xl font-bold">Meu perfil</h1>
    <div class="card">
      <p v-if="loading">Carregando...</p>
      <p v-else-if="error" class="text-rose-600">{{ error }}</p>
      <dl v-else class="divide-y divide-slate-200">
        <div class="grid gap-1 py-4 sm:grid-cols-[10rem_1fr]">
          <dt class="font-medium text-slate-500">Nome</dt>
          <dd>{{ auth.user?.name }}</dd>
        </div>
        <div class="grid gap-1 py-4 sm:grid-cols-[10rem_1fr]">
          <dt class="font-medium text-slate-500">E-mail</dt>
          <dd>{{ auth.user?.email }}</dd>
        </div>
      </dl>
    </div>
  </section>
</template>
