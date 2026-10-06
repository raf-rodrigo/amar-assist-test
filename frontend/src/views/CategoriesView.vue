<script setup>
import { onMounted, reactive, ref } from 'vue'
import { api } from '../api/client'
import PaginationBar from '../components/PaginationBar.vue'
import ConfirmationModal from '../components/ConfirmationModal.vue'

const items = ref([]), meta = ref(null), search = ref(''), error = ref('')
const itemToRemove = ref(null), removing = ref(false)
const form = reactive({ id: null, description: '' })

async function load(page = 1) {
  const { data } = await api.get('/categories', { params: { search: search.value, page } })
  items.value = data.data; meta.value = data
}
function edit(item) { form.id = item.id; form.description = item.description }
function reset() { form.id = null; form.description = ''; error.value = '' }
async function save() {
  error.value = ''
  try {
    form.id ? await api.put(`/categories/${form.id}`, form) : await api.post('/categories', form)
    reset(); await load()
  } catch (exception) { error.value = exception.response?.data?.message || 'Não foi possível salvar.' }
}
function requestRemove(item) { itemToRemove.value = item }
async function remove() {
  if (!itemToRemove.value) return
  removing.value = true
  try {
    await api.delete(`/categories/${itemToRemove.value.id}`)
    itemToRemove.value = null
    await load()
  }
  catch (exception) { error.value = exception.response?.data?.message || 'Não foi possível excluir.' }
  finally { removing.value = false }
}
onMounted(load)
</script>

<template>
  <section class="w-full">
    <h1 class="mb-6 text-3xl font-bold">Categorias</h1>
    <div class="grid gap-6 lg:grid-cols-[1fr_2fr]">
      <form class="card space-y-4" @submit.prevent="save">
        <h2 class="text-xl font-semibold">{{ form.id ? 'Editar categoria' : 'Nova categoria' }}</h2>
        <div><label class="label">Descrição</label><input v-model="form.description" class="input" maxlength="191" required></div>
        <p v-if="error" class="text-sm text-rose-600">{{ error }}</p>
        <div class="flex gap-2"><button class="btn">Salvar</button><button v-if="form.id" class="btn-secondary" type="button" @click="reset">Cancelar</button></div>
      </form>
      <div class="card">
        <form class="mb-4 flex flex-col gap-2 sm:flex-row" @submit.prevent="load()"><input v-model="search" class="input" placeholder="Pesquisar categorias"><button class="btn">Pesquisar</button></form>
        <div v-for="item in items" :key="item.id" class="flex items-center border-b py-3 last:border-0">
          <span class="mr-auto">{{ item.description }}</span><button class="btn-secondary mr-2" @click="edit(item)">Editar</button><button class="btn-danger" @click="requestRemove(item)">Excluir</button>
        </div>
        <PaginationBar :meta="meta" @change="load" />
      </div>
    </div>
    <ConfirmationModal
      v-if="itemToRemove"
      title="Excluir categoria"
      :message="`Deseja excluir a categoria “${itemToRemove.description}”?`"
      confirm-text="Excluir"
      danger
      :loading="removing"
      @confirm="remove"
      @cancel="itemToRemove = null"
    />
  </section>
</template>
