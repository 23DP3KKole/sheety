<script setup>
import { onMounted, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import api from '../api/client'

const route = useRoute()
const router = useRouter()

const editId = route.query.edit
const form = ref({ title: '', artist: '', content: '' })
const loading = ref(false)
const error = ref('')

onMounted(async () => {
  if (!editId) return
  const { data } = await api.get(`/tabs/${editId}`)
  const tab = data.tab
  form.value = { title: tab.title, artist: tab.artist, content: tab.content }
})

async function submit() {
  loading.value = true
  error.value = ''
  try {
    if (editId) {
      await api.put(`/tabs/${editId}`, form.value)
      router.push({ name: 'tab-detail', params: { id: editId } })
    } else {
      const { data } = await api.post('/tabs', form.value)
      router.push({ name: 'tab-detail', params: { id: data.tab.id } })
    }
  } catch (e) {
    error.value = e.response?.data?.message || 'Could not save tab.'
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <section class="max-w-2xl">
    <h1 class="text-3xl font-bold mb-6">{{ editId ? 'Edit tab' : 'Upload tab' }}</h1>
    <p v-if="error" class="mb-4 text-sm text-red-700">{{ error }}</p>
    <form class="space-y-4" @submit.prevent="submit">
      <label class="field">
        <span>Title</span>
        <input v-model="form.title" type="text" required class="input" />
      </label>
      <label class="field">
        <span>Artist</span>
        <input v-model="form.artist" type="text" required class="input" />
      </label>
      <label class="field">
        <span>Tab content</span>
        <textarea v-model="form.content" required rows="14" class="input font-mono text-sm" />
      </label>
      <button type="submit" class="btn-primary px-6" :disabled="loading">
        {{ loading ? 'Saving…' : editId ? 'Update' : 'Upload' }}
      </button>
    </form>
  </section>
</template>

<style scoped>
@reference "../style.css";

.field {
  @apply block;
}

.field span {
  @apply block text-sm font-medium mb-1;
}

.input {
  @apply w-full px-4 py-2.5 rounded-lg border border-ink/15 bg-surface focus:outline-none focus:ring-2 focus:ring-amber/40;
}

.btn-primary {
  @apply py-2.5 rounded-lg bg-amber text-white font-medium hover:bg-amber-dark transition disabled:opacity-60;
}
</style>
