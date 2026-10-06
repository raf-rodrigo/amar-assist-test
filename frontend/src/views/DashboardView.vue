<script setup>
import { onMounted, ref } from 'vue'
import { api } from '../api/client'

const summary = ref(null)
onMounted(async () => { summary.value = (await api.get('/dashboard')).data })
const money = (value) => new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(value)
</script>

<template>
  <section class="w-full">
    <h1 class="mb-6 text-3xl font-bold">Resumo do mês atual</h1>
    <div v-if="summary" class="grid gap-5 md:grid-cols-3">
      <article class="card border-l-4 border-brand-500"><p class="text-slate-500">Receitas</p><strong class="text-2xl text-brand-700">{{ money(summary.income) }}</strong></article>
      <article class="card border-l-4 border-accent-500"><p class="text-slate-500">Despesas</p><strong class="text-2xl text-accent-600">{{ money(summary.expense) }}</strong></article>
      <article class="card border-l-4 border-brand-800"><p class="text-slate-500">Saldo</p><strong class="text-2xl text-brand-800">{{ money(summary.balance) }}</strong></article>
    </div>
    <p v-else>Carregando...</p>
  </section>
</template>
