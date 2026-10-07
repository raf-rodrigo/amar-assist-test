<script setup>
import { computed, onMounted, ref } from 'vue'
import { api } from '../api/client'

const summary = ref(null)
const loading = ref(false)
const error = ref('')
const currentDate = new Date()
const selectedMonth = ref(`${currentDate.getFullYear()}-${String(currentDate.getMonth() + 1).padStart(2, '0')}`)
const monthOptions = computed(() => {
  const options = []
  const start = new Date(currentDate.getFullYear(), currentDate.getMonth() - 12, 1)

  for (let index = 0; index < 25; index += 1) {
    const date = new Date(start.getFullYear(), start.getMonth() + index, 1)
    const value = `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, '0')}`
    const label = new Intl.DateTimeFormat('pt-BR', { month: 'long', year: 'numeric' }).format(date)
    options.push({ value, label: label.charAt(0).toUpperCase() + label.slice(1) })
  }

  return options
})

async function load() {
  loading.value = true
  error.value = ''

  try {
    summary.value = (await api.get('/dashboard', { params: { month: selectedMonth.value } })).data
  } catch (exception) {
    error.value = exception.response?.data?.message || 'Não foi possível carregar o dashboard.'
  } finally {
    loading.value = false
  }
}

onMounted(load)
const money = (value) => new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(value)
</script>

<template>
  <section class="w-full">
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
      <div>
        <h1 class="text-3xl font-bold">Resumo financeiro</h1>
        <p class="mt-1 text-slate-500">Consulte receitas, despesas e saldo por mês.</p>
      </div>
      <div class="w-full sm:w-64">
        <label class="label" for="reference-month">Mês de referência</label>
        <select id="reference-month" v-model="selectedMonth" class="input" :disabled="loading" @change="load">
          <option v-for="option in monthOptions" :key="option.value" :value="option.value">{{ option.label }}</option>
        </select>
      </div>
    </div>
    <p v-if="error" class="mb-4 text-sm text-rose-600">{{ error }}</p>
    <div v-if="summary" class="grid gap-5 md:grid-cols-3">
      <article class="card border-l-4 border-brand-500"><p class="text-slate-500">Receitas</p><strong class="text-2xl text-brand-700">{{ money(summary.income) }}</strong></article>
      <article class="card border-l-4 border-accent-500"><p class="text-slate-500">Despesas</p><strong class="text-2xl text-accent-600">{{ money(summary.expense) }}</strong></article>
      <article class="card border-l-4 border-brand-800"><p class="text-slate-500">Saldo</p><strong class="text-2xl text-brand-800">{{ money(summary.balance) }}</strong></article>
    </div>
    <p v-else-if="loading">Carregando...</p>
  </section>
</template>
