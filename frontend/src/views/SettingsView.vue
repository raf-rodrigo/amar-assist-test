<script setup>
import { onMounted, ref } from 'vue'
import { api } from '../api/client'

const reminderTime = ref('08:00')
const loading = ref(true)
const saving = ref(false)
const message = ref('')
const error = ref('')

async function load() {
  try {
    reminderTime.value = (await api.get('/settings')).data.reminder_time || '08:00'
  } catch (exception) {
    error.value = exception.response?.data?.message || 'Não foi possível carregar as configurações.'
  } finally {
    loading.value = false
  }
}

async function save() {
  saving.value = true
  message.value = ''
  error.value = ''
  try {
    const { data } = await api.patch('/settings', { reminder_time: reminderTime.value })
    reminderTime.value = data.reminder_time
    message.value = data.message
  } catch (exception) {
    error.value = exception.response?.data?.errors?.reminder_time?.[0] || exception.response?.data?.message || 'Não foi possível salvar as configurações.'
  } finally {
    saving.value = false
  }
}

onMounted(load)
</script>

<template>
  <section class="mx-auto w-full max-w-2xl">
    <h1 class="mb-2 text-3xl font-bold">Configurações</h1>
    <p class="mb-6 text-slate-500">Defina quando deseja receber o aviso de despesas com vencimento no dia seguinte.</p>
    <div class="card">
      <p v-if="loading">Carregando...</p>
      <form v-else class="space-y-5" @submit.prevent="save">
        <div>
          <label class="label" for="reminder-time">Horário do aviso</label>
          <input id="reminder-time" v-model="reminderTime" class="input max-w-xs" type="time" required>
          <p class="mt-2 text-sm text-slate-500">O envio será processado automaticamente todos os dias nesse horário.</p>
        </div>
        <p v-if="message" class="text-sm text-emerald-700">{{ message }}</p>
        <p v-if="error" class="text-sm text-rose-600">{{ error }}</p>
        <button class="btn" type="submit" :disabled="saving">{{ saving ? 'Salvando...' : 'Salvar configurações' }}</button>
      </form>
    </div>
  </section>
</template>
