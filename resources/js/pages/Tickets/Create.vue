<script setup>
import { useForm } from '@inertiajs/vue3'
import { ref } from 'vue';

defineProps({
  projects: Array,
})

const form = useForm({
  project_id: '',
  title: '',
  description: '',
  attachment: null,
})

function submit() {
  form.post('/tickets', {
    forceFormData: true,
  })
}

const fileInputRef = ref(null);

function openAttachmentFile() {
    fileInputRef.value?.click();
    console.log(fileInputRef)
}
</script>

<template>
  <div class="max-w-xl w-full mx-auto p-6">
    <h1 class="text-2xl font-bold mb-6">Novo Ticket</h1>

    <div class="space-y-4">
      <div>
        <label class="block mb-1 font-medium">Projeto</label>
        <select v-model="form.project_id" class="border rounded w-full p-2">
          <option class="text-black" value="">Selecione...</option>
          <option class="text-black" v-for="p in projects" :key="p.id" :value="p.id">{{ p.name }}</option>
        </select>
        <p v-if="form.errors.project_id" class="text-red-600 text-sm mt-1">{{ form.errors.project_id }}</p>
      </div>

      <div>
        <label class="block mb-1 font-medium">Título</label>
        <input v-model="form.title" type="text" class="border rounded w-full p-2" />
        <p v-if="form.errors.title" class="text-red-600 text-sm mt-1">{{ form.errors.title }}</p>
      </div>

      <div>
        <label class="block mb-1 font-medium">Descrição</label>
        <textarea v-model="form.description" rows="4" class="border rounded w-full p-2"></textarea>
      </div>

      <div>
        <label class="block mb-1 font-medium">Anexo (JSON ou TXT)</label>
        <input ref="fileInputRef" class="hidden" type="file" @input="form.attachment = $event.target.files[0]" />
        <button
            class="text-whiterounded disabled:opacity-50 cursor-pointer underline"
            @click="openAttachmentFile"
        >
            Selecionar arquivo
        </button>
        <div v-if="form.attachment">
            <span>Arquivo: <strong>{{ form.attachment.name }}</strong></span>
        </div>
        <p v-if="form.errors.attachment" class="text-red-600 text-sm mt-1">{{ form.errors.attachment }}</p>
      </div>

      <button
        @click="submit"
        :disabled="form.processing"
        class="bg-blue-600 text-white px-4 py-2 rounded disabled:opacity-50 cursor-pointer"
      >
        Criar
      </button>
    </div>
  </div>
</template>
