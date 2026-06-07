<script setup>
import { Link } from '@inertiajs/vue3'

defineProps({
  tickets: Array,
})
</script>

<template>
  <div class="max-w-4xl mx-auto p-6">
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold">Tickets</h1>
      <Link href="/tickets/create" class="bg-blue-600 text-white px-4 py-2 rounded">Novo Ticket</Link>
    </div>

    <table class="w-full border-collapse">
      <thead>
        <tr class="text-left">
          <th class="sm:p-3 p-1 text-sm sm:text-base">Título</th>
          <th class="sm:p-3 p-1 text-sm sm:text-base">Projeto</th>
          <th class="sm:p-3 p-1 text-sm sm:text-base">Status</th>
          <th class="sm:p-3 p-1 text-sm sm:text-base">Prioridade</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="t in tickets" :key="t.id" class="border-t hover:bg-black/95">
          <td class="sm:p-3 p-1 text-sm sm:text-base">
            <Link :href="`/tickets/${t.id}`" class="text-blue-600 hover:underline">{{ t.title }}</Link>
          </td>
          <td class="sm:p-3 p-1 text-sm sm:text-base">{{ t.project?.name }}</td>
          <td class="sm:p-3 p-1 text-sm sm:text-base">{{ t.status }}</td>
          <td class="sm:p-3 p-1 text-sm sm:text-base">{{ t.detail?.priority ?? '—' }}</td>
        </tr>
        <tr v-if="tickets.length === 0">
          <td colspan="4" class="p-3 text-center text-gray-500">Nenhum ticket ainda.</td>
        </tr>
      </tbody>
    </table>
  </div>
</template>
