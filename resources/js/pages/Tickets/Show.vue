<script setup>
import { useForm } from '@inertiajs/vue3'


defineProps({
  ticket: Object,
})

function destroy(id) {
    if (!confirm('Excluir este ticket?')) return
    useForm({}).delete(`/tickets/${id}`)
}
</script>

<template>
  <div class="max-w-2xl mx-auto p-6">
    <h1 class="text-2xl font-bold mb-2">{{ ticket.title }}</h1>
    <p class="text-gray-600 mb-6">{{ ticket.description }}</p>

    <dl class="space-y-2">
      <div><dt class="font-medium inline">Projeto:</dt> {{ ticket.project?.name }}</div>
      <div><dt class="font-medium inline">Responsável:</dt> {{ ticket.user?.name }}</div>
      <div><dt class="font-medium inline">Status:</dt> {{ ticket.status }}</div>
      <div><dt class="font-medium inline">Prioridade:</dt> {{ ticket.detail?.priority ?? '—' }}</div>
      <div><dt class="font-medium inline">Categoria:</dt> {{ ticket.detail?.category ?? '—' }}</div>
      <div><dt class="font-medium inline">Processado em:</dt> {{ ticket.detail?.processed_at ?? 'pendente' }}</div>
    </dl>
  </div>

  <button @click="destroy(ticket.id)" class="text-red-400 cursor-pointer">Deletar ticket</button>

</template>
