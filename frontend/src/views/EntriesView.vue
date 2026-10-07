<script setup>
import { computed, onMounted, reactive, ref, watch } from 'vue'
import { api } from '../api/client'
import PaginationBar from '../components/PaginationBar.vue'
import ConfirmationModal from '../components/ConfirmationModal.vue'

const props = defineProps({ type: { type: String, required: true } })
const items = ref([]), categories = ref([]), meta = ref(null), search = ref(''), errors = ref({})
const itemToRemove = ref(null), removing = ref(false)
const sortBy = ref('date'), sortDirection = ref('desc')
const formModalOpen = ref(false), saving = ref(false)
const form = reactive({ id: null, category_id: '', date: '', description: '', amount: '' })
const title = computed(() => props.type === 'incomes' ? 'Receitas' : 'Despesas')
const singularTitle = computed(() => props.type === 'incomes' ? 'receita' : 'despesa')
const today = new Date().toLocaleDateString('en-CA')
const maxDate = computed(() => {
  const date = new Date()
  if (props.type === 'incomes') return new Date(date.getFullYear(), date.getMonth() + 1, 0).toLocaleDateString('en-CA')
  date.setFullYear(date.getFullYear() + 1)
  return date.toLocaleDateString('en-CA')
})
const money = (value) => new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(value)
const formatDate = (value) => new Intl.DateTimeFormat('pt-BR', { timeZone: 'UTC' }).format(new Date(value))
const formatAmount = (value) => {
  if (value === '' || value === null || value === undefined) return ''
  const number = Number(value)
  return Number.isFinite(number) ? money(number) : ''
}
const parseAmount = (value) => {
  const digits = String(value).replace(/\D/g, '')
  return digits ? (Number(digits) / 100).toFixed(2) : ''
}

function maskAmount(event) {
  form.amount = formatAmount(parseAmount(event.target.value))
}

async function load(page = 1) {
  const { data } = await api.get(`/${props.type}`, {
    params: { search: search.value, page, sort_by: sortBy.value, sort_direction: sortDirection.value },
  })
  items.value = data.data; meta.value = data
}

async function sort(column) {
  if (sortBy.value === column) {
    sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc'
  } else {
    sortBy.value = column
    sortDirection.value = 'asc'
  }
  await load()
}

function sortIcon(column) {
  if (sortBy.value !== column) return '↕'
  return sortDirection.value === 'asc' ? '↑' : '↓'
}
async function loadCategories() {
  const { data } = await api.get('/categories')
  categories.value = data.data
}
function edit(item) {
  Object.assign(form, { id: item.id, category_id: item.category_id, date: item.date.slice(0, 10), description: item.description, amount: formatAmount(item.amount) })
  errors.value = {}
  formModalOpen.value = true
}
function reset() { Object.assign(form, { id: null, category_id: '', date: '', description: '', amount: '' }); errors.value = {} }
function createNew() { reset(); formModalOpen.value = true }
function closeForm() { if (!saving.value) formModalOpen.value = false }
async function save() {
  errors.value = {}
  saving.value = true
  try {
    const payload = { ...form, amount: parseAmount(form.amount) }
    form.id ? await api.put(`/${props.type}/${form.id}`, payload) : await api.post(`/${props.type}`, payload)
    reset(); formModalOpen.value = false; await load()
  } catch (exception) { errors.value = exception.response?.data?.errors || { general: [exception.response?.data?.message || 'Não foi possível salvar.'] } }
  finally { saving.value = false }
}
function requestRemove(item) { itemToRemove.value = item }
async function remove() {
  if (!itemToRemove.value) return
  removing.value = true
  try {
    await api.delete(`/${props.type}/${itemToRemove.value.id}`)
    itemToRemove.value = null
    await load()
  } catch (exception) {
    errors.value = { general: [exception.response?.data?.message || 'Não foi possível excluir.'] }
  } finally {
    removing.value = false
  }
}
async function initialize() { reset(); formModalOpen.value = false; itemToRemove.value = null; search.value = ''; sortBy.value = 'date'; sortDirection.value = 'desc'; await Promise.all([load(), loadCategories()]) }
watch(() => props.type, initialize)
let searchTimer
watch(search, () => {
  clearTimeout(searchTimer)
  searchTimer = setTimeout(() => load(), 300)
})
onMounted(initialize)
</script>

<template>
  <section class="w-full">
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
      <h1 class="text-3xl font-bold">{{ title }}</h1>
      <button class="btn" type="button" @click="createNew">Nova {{ singularTitle }}</button>
    </div>
    <div class="card">
        <form class="mb-4 flex flex-col gap-2 sm:flex-row" @submit.prevent="load()"><input v-model="search" class="input" placeholder="Pesquisar descrição, categoria, data ou valor"><button class="btn">Pesquisar</button></form>
        <table class="w-full table-fixed text-left text-xs sm:text-sm"><thead><tr class="border-b"><th class="w-[22%] py-3 sm:w-[15%]"><button class="flex items-center gap-1 font-semibold hover:text-brand-700" type="button" @click="sort('date')">Data <span aria-hidden="true">{{ sortIcon('date') }}</span></button></th><th class="w-[28%] sm:w-[30%]"><button class="flex items-center gap-1 font-semibold hover:text-brand-700" type="button" @click="sort('description')">Descrição <span aria-hidden="true">{{ sortIcon('description') }}</span></button></th><th class="w-[22%] border-l border-slate-200 pl-2 sm:w-[22%]"><button class="flex items-center gap-1 pl-2 font-semibold hover:text-brand-700" type="button" @click="sort('category')">Categoria <span aria-hidden="true">{{ sortIcon('category') }}</span></button></th><th class="w-[13%] pl-2 sm:w-[13%]"><button class="flex items-center gap-1 font-semibold hover:text-brand-700" type="button" @click="sort('amount')">Valor <span aria-hidden="true">{{ sortIcon('amount') }}</span></button></th><th class="w-[15%] sm:w-[20%]"></th></tr></thead>
          <tbody><tr v-for="item in items" :key="item.id" class="border-b align-middle last:border-0"><td class="py-3">{{ formatDate(item.date) }}</td><td class="wrap-text py-3">{{ item.description }}</td><td class="wrap-text border-l border-slate-100 py-3 pl-2 text-slate-600">{{ item.category.description }}</td><td class="py-3 pl-2">{{ money(item.amount) }}</td><td class="py-3"><div class="flex flex-col gap-1 sm:flex-row sm:justify-end"><button class="btn-secondary px-2 py-1 text-xs sm:mr-1" @click="edit(item)">Editar</button><button class="btn-danger px-2 py-1 text-xs" @click="requestRemove(item)">Excluir</button></div></td></tr></tbody>
        </table>
        <p v-if="!items.length" class="py-6 text-center text-slate-500">Nenhum registro encontrado.</p>
        <PaginationBar :meta="meta" @change="load" />
    </div>
    <div v-if="formModalOpen" class="fixed inset-0 z-40 flex items-center justify-center bg-slate-950/60 p-4" role="dialog" aria-modal="true" @click.self="closeForm">
      <form class="max-h-[90vh] w-full max-w-lg space-y-4 overflow-y-auto rounded-2xl bg-white p-6 shadow-2xl sm:p-8" @submit.prevent="save">
        <div class="flex items-start justify-between gap-4">
          <div>
            <h2 class="text-xl font-semibold">{{ form.id ? `Editar ${singularTitle}` : `Nova ${singularTitle}` }}</h2>
            <p class="mt-1 text-sm text-slate-500"><span class="text-accent-600">*</span> Todos os campos são obrigatórios.</p>
          </div>
          <button class="rounded-lg px-3 py-1 text-2xl text-slate-500 hover:bg-slate-100" type="button" aria-label="Fechar formulário" :disabled="saving" @click="closeForm">×</button>
        </div>
        <div><label class="label">Categoria <span class="text-accent-600">*</span></label><select v-model="form.category_id" class="input" required><option disabled value="">Selecione...</option><option v-for="category in categories" :key="category.id" :value="category.id">{{ category.description }}</option></select><small class="text-rose-600">{{ errors.category_id?.[0] }}</small></div>
        <div><label class="label">Data <span class="text-accent-600">*</span></label><input v-model="form.date" class="input" type="date" :min="today" :max="maxDate" required><small class="text-rose-600">{{ errors.date?.[0] }}</small></div>
        <div><label class="label">Descrição <span class="text-accent-600">*</span></label><input v-model="form.description" class="input" maxlength="191" required><small class="text-rose-600">{{ errors.description?.[0] }}</small></div>
        <div><label class="label">Valor <span class="text-accent-600">*</span></label><input v-model="form.amount" class="input" type="text" inputmode="decimal" placeholder="R$ 0,00" required @input="maskAmount"><small class="text-rose-600">{{ errors.amount?.[0] }}</small></div>
        <p v-if="errors.general" class="text-sm text-rose-600">{{ errors.general[0] }}</p>
        <div class="flex justify-end gap-2"><button class="btn-secondary" type="button" :disabled="saving" @click="closeForm">Cancelar</button><button class="btn" :disabled="saving">{{ saving ? 'Salvando...' : 'Salvar' }}</button></div>
      </form>
    </div>
    <ConfirmationModal
      v-if="itemToRemove"
      :title="`Excluir ${singularTitle}`"
      :message="`Deseja excluir “${itemToRemove.description}”?`"
      confirm-text="Excluir"
      danger
      :loading="removing"
      @confirm="remove"
      @cancel="itemToRemove = null"
    />
  </section>
</template>
