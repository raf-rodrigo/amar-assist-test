<script setup>
import { computed, ref } from 'vue'
import { useAuthStore } from './stores/auth'

const auth = useAuthStore()
const profileMenuOpen = ref(false)
const currentYear = new Date().getFullYear()
const initials = computed(() => (auth.user?.name || 'U')
  .split(' ')
  .slice(0, 2)
  .map((part) => part[0])
  .join('')
  .toUpperCase())

async function logout() {
  profileMenuOpen.value = false
  await auth.logout()
}
</script>

<template>
  <div class="flex min-h-screen flex-col bg-[#f7f5f2] text-slate-900">
    <header v-if="auth.isAuthenticated && !$route.meta.guest" class="bg-brand-200 text-brand-900 shadow">
      <nav class="mx-auto flex max-w-6xl flex-wrap items-center gap-3 px-6 py-3">
        <RouterLink class="mr-auto flex items-center gap-3 text-lg font-bold" to="/">
          <img class="h-10 w-auto rounded-md object-contain" src="/logo.png" alt="Logo do Gerenciador Financeiro">
          <span class="hidden sm:inline">Gerenciador Financeiro</span>
        </RouterLink>

        <div class="order-3 flex w-full flex-wrap items-center justify-center gap-x-4 gap-y-2 border-t border-brand-300 pt-3 text-sm md:order-none md:w-auto md:border-0 md:pt-0">
          <RouterLink class="hover:text-accent-700" to="/">Dashboard</RouterLink>
          <RouterLink class="hover:text-accent-700" to="/categories">Categorias</RouterLink>
          <RouterLink class="hover:text-accent-700" to="/incomes">Receitas</RouterLink>
          <RouterLink class="hover:text-accent-700" to="/expenses">Despesas</RouterLink>
        </div>

        <div class="relative">
          <button
            class="flex items-center gap-2 rounded-lg p-1.5 hover:bg-brand-300"
            type="button"
            aria-label="Abrir menu do usuário"
            :aria-expanded="profileMenuOpen"
            @click="profileMenuOpen = !profileMenuOpen"
          >
            <span class="flex h-9 w-9 items-center justify-center rounded-full bg-accent-500 text-sm font-bold text-white">{{ initials }}</span>
            <span class="hidden max-w-28 truncate text-sm md:block">{{ auth.user?.name }}</span>
            <span class="text-xs">▼</span>
          </button>

          <div v-if="profileMenuOpen" class="absolute right-0 z-20 mt-2 w-60 rounded-xl bg-white py-2 text-slate-800 shadow-xl ring-1 ring-slate-200">
            <div class="border-b border-slate-100 px-4 py-3">
              <p class="truncate font-semibold">{{ auth.user?.name }}</p>
              <p class="truncate text-sm text-slate-500">{{ auth.user?.email }}</p>
            </div>
            <RouterLink class="block px-4 py-3 text-sm hover:bg-slate-50" to="/profile" @click="profileMenuOpen = false">
              Meu perfil
            </RouterLink>
            <button class="block w-full px-4 py-3 text-left text-sm text-rose-600 hover:bg-rose-50" type="button" @click="logout">
              Sair
            </button>
          </div>
        </div>
      </nav>
    </header>
    <main
      class="mx-auto flex w-full max-w-6xl flex-1 flex-col px-4 py-6 sm:px-6"
      :class="{ 'items-center justify-center': $route.meta.guest }"
    >
      <RouterView />
    </main>
    <footer class="border-t border-brand-100 bg-white px-4 py-5 text-center text-sm text-brand-700">
      Teste Prático - Desenvolvedor PHP by Rafael Rodrigo Doimo ® {{ currentYear }}
    </footer>
  </div>
</template>
