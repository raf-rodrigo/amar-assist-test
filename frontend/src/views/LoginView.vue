<script setup>
import { reactive, ref } from 'vue'
import { useAuthStore } from '../stores/auth'

const auth = useAuthStore()
const form = reactive({ email: 'demo@example.com', password: 'password' })
const error = ref('')
const loading = ref(false)

async function submit() {
  loading.value = true
  error.value = ''
  try { await auth.login(form) }
  catch (exception) { error.value = exception.response?.data?.message || 'Não foi possível entrar.' }
  finally { loading.value = false }
}
</script>

<template>
  <section class="w-full max-w-md card">
    <img class="mx-auto mb-4 h-20 w-auto object-contain" src="/logo.png" alt="Logo do Gerenciador Financeiro">
    <h1 class="mb-1 text-center text-2xl font-bold">Gerenciador Financeiro</h1>
    <p class="mb-6 text-center text-slate-500">Entre para organizar suas finanças.</p>
    <form class="space-y-4" @submit.prevent="submit">
      <div><label class="label">E-mail</label><input v-model="form.email" class="input" type="email" required></div>
      <div><label class="label">Senha</label><input v-model="form.password" class="input" type="password" required></div>
      <p v-if="error" class="text-sm text-rose-600">{{ error }}</p>
      <button class="btn w-full" :disabled="loading">{{ loading ? 'Entrando...' : 'Entrar' }}</button>
    </form>
  </section>
</template>
