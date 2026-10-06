<script setup>
import { computed, onMounted, reactive, ref, watch } from 'vue'
import { api } from '../api/client'
import PaginationBar from '../components/PaginationBar.vue'
import ConfirmationModal from '../components/ConfirmationModal.vue'

const props = defineProps({ type: { type: String, required: true } })
const items = ref([]), categories = ref([]), meta = ref(null), search = ref(''), errors = ref({})
const itemToRemove = ref(null), removing = ref(false)
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
  const { data } = await api.get(`/${props.type}`, { params: { search: search.value, page } })
  items.value = data.data; meta.value = data
}
async function loadCategories() {
  const { data } = await api.get('/categories')
  categories.value = data.data
}
function edit(item) {
  Object.assign(form, { id: item.id, category_id: item.category_id, date: item.date.slice(0, 10), description: item.description, amount: formatAmount(item.amount) })
}
function reset() { Object.assign(form, { id: null, category_id: '', date: '', description: '', amount: '' }); errors.value = {} }
async function save() {
  errors.value = {}
  try {
    const payload = { ...form, amount: parseAmount(form.amount) }
    form.id ? await api.put(`/${props.type}/${form.id}`, payload) : await api.post(`/${props.type}`, payload)
    reset(); await load()
  } catch (exception) { errors.value = exception.response?.data?.errors || { general: [exception.response?.data?.message || 'Não foi possível salvar.'] } }
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
async function initialize() { reset(); itemToRemove.value = null; search.value = ''; await Promise.all([load(), loadCategories()]) }
watch(() => props.type, initialize)
onMounted(initialize)
</script>

<template>
  <section class="w-full">
    <h1 class="mb-6 text-3xl font-bold">{{ title }}</h1>
    <div class="grid gap-6 lg:grid-cols-[1fr_2fr]">
      <form class="card space-y-4" @submit.prevent="save">
        <h2 class="text-xl font-semibold">{{ form.id ? `Editar ${singularTitle}` : `Nova ${singularTitle}` }}</h2>
        <p class="text-sm text-slate-500"><span class="text-accent-600">*</span> Todos os campos são obrigatórios.</p>
        <div><label class="label">Categoria <span class="text-accent-600">*</span></label><select v-model="form.category_id" class="input" required><option disabled value="">Selecione...</option><option v-for="category in categories" :key="category.id" :value="category.id">{{ category.description }}</option></select><small class="text-rose-600">{{ errors.category_id?.[0] }}</small></div>
        <div><label class="label">Data <span class="text-accent-600">*</span></label><input v-model="form.date" class="input" type="date" :min="today" :max="maxDate" required><small class="text-rose-600">{{ errors.date?.[0] }}</small></div>
        <div><label class="label">Descrição <span class="text-accent-600">*</span></label><input v-model="form.description" class="input" maxlength="191" required><small class="text-rose-600">{{ errors.description?.[0] }}</small></div>
        <div><label class="label">Valor <span class="text-accent-600">*</span></label><input v-model="form.amount" class="input" type="text" inputmode="decimal" placeholder="R$ 0,00" required @input="maskAmount"><small class="text-rose-600">{{ errors.amount?.[0] }}</small></div>
        <p v-if="errors.general" class="text-sm text-rose-600">{{ errors.general[0] }}</p>
        <div class="flex gap-2"><button class="btn">Salvar</button><button v-if="form.id" class="btn-secondary" type="button" @click="reset">Cancelar</button></div>
      </form>
      <div class="card overflow-x-auto">
        <form class="mb-4 flex flex-col gap-2 sm:flex-row" @submit.prevent="load()"><input v-model="search" class="input" placeholder="Pesquisar descrição ou categoria"><button class="btn">Pesquisar</button></form>
        <table class="min-w-[700px] w-full text-left"><thead><tr class="border-b"><th class="py-3">Data</th><th>Descrição</th><th>Categoria</th><th>Valor</th><th></th></tr></thead>
          <tbody><tr v-for="item in items" :key="item.id" class="border-b last:border-0"><td class="py-3">{{ formatDate(item.date) }}</td><td>{{ item.description }}</td><td>{{ item.category.description }}</td><td>{{ money(item.amount) }}</td><td class="whitespace-nowrap text-right"><button class="btn-secondary mr-2" @click="edit(item)">Editar</button><button class="btn-danger" @click="requestRemove(item)">Excluir</button></td></tr></tbody>
        </table>
        <p v-if="!items.length" class="py-6 text-center text-slate-500">Nenhum registro encontrado.</p>
        <PaginationBar :meta="meta" @change="load" />
      </div>
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
