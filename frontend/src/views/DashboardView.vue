<script setup>
import { computed, onBeforeUnmount, onMounted, ref } from 'vue'
import { api } from '../api/client'

const summary = ref(null)
const loading = ref(false)
const error = ref('')
const currentDate = new Date()
const selectedMonth = ref(`${currentDate.getFullYear()}-${String(currentDate.getMonth() + 1).padStart(2, '0')}`)
const monthSearch = ref('')
const monthDropdownOpen = ref(false)
const monthContainer = ref(null)
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
const selectedMonthLabel = computed(() => monthOptions.value.find((option) => option.value === selectedMonth.value)?.label || '')
const filteredMonthOptions = computed(() => {
  const searchTerm = monthSearch.value.trim().toLocaleLowerCase('pt-BR').normalize('NFD').replace(/[\u0300-\u036f]/g, '')
  if (!searchTerm) return monthOptions.value
  return monthOptions.value.filter((option) => option.label.toLocaleLowerCase('pt-BR').normalize('NFD').replace(/[\u0300-\u036f]/g, '').includes(searchTerm))
})

function openMonthDropdown() {
  if (loading.value) return
  monthSearch.value = ''
  monthDropdownOpen.value = true
}
function closeMonthDropdown() {
  monthDropdownOpen.value = false
  monthSearch.value = ''
}
async function selectMonth(option) {
  selectedMonth.value = option.value
  closeMonthDropdown()
  await load()
}
function closeMonthDropdownWhenClickingOutside(event) {
  if (monthContainer.value && !monthContainer.value.contains(event.target)) closeMonthDropdown()
}

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
onMounted(() => document.addEventListener('click', closeMonthDropdownWhenClickingOutside))
onBeforeUnmount(() => document.removeEventListener('click', closeMonthDropdownWhenClickingOutside))
const money = (value) => new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(value)
</script>

<template>
  <section class="w-full">
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
      <div>
        <h1 class="text-3xl font-bold">Resumo financeiro</h1>
        <p class="mt-1 text-slate-500">Consulte receitas, despesas e saldo por mês.</p>
      </div>
      <div ref="monthContainer" class="relative w-full sm:w-64">
        <label class="label" for="reference-month">Mês de referência</label>
        <button v-if="!monthDropdownOpen" id="reference-month" class="input flex items-center justify-between text-left" type="button" :disabled="loading" @click.stop="openMonthDropdown">
          <span>{{ selectedMonthLabel }}</span><span aria-hidden="true">⌄</span>
        </button>
        <div v-else class="relative">
          <input id="reference-month" v-model="monthSearch" class="input" type="search" placeholder="Pesquisar mês ou ano" autofocus @click.stop @keydown.esc="closeMonthDropdown">
          <div class="absolute z-20 mt-1 max-h-64 w-full overflow-y-auto rounded-lg border border-slate-200 bg-white p-1 shadow-xl">
            <button v-for="option in filteredMonthOptions" :key="option.value" class="block w-full rounded-md px-3 py-2 text-left text-sm hover:bg-brand-50" type="button" @click="selectMonth(option)">{{ option.label }}</button>
            <p v-if="!filteredMonthOptions.length" class="px-3 py-2 text-sm text-slate-500">Nenhum mês encontrado.</p>
          </div>
        </div>
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
