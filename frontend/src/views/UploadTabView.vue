<script setup>
import { onMounted, reactive, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import FormField from '../components/FormField.vue'
import api from '../api/client'
import { useNotificationStore } from '../stores/notifications'
import { handleApiValidationError } from '../utils/apiErrors'
import { hasErrors, validateFields, validators } from '../utils/validation'

const route = useRoute()
const router = useRouter()
const notify = useNotificationStore()

const editId = route.query.edit
const form = ref({ title: '', artist: '', content: '' })
const fieldErrors = reactive({})
const loading = ref(false)

function clearError(field) {
  delete fieldErrors[field]
}

onMounted(async () => {
  if (!editId) return
  const { data } = await api.get(`/tabs/${editId}`)
  const tab = data.tab
  form.value = { title: tab.title, artist: tab.artist, content: tab.content }
})

async function submit() {
  for (const key of Object.keys(fieldErrors)) delete fieldErrors[key]

  const errors = validateFields(form.value, {
    title: validators.title,
    artist: validators.artist,
    content: validators.content,
  })

  if (hasErrors(errors)) {
    Object.assign(fieldErrors, errors)
    notify.error('Please fix the errors before saving.')
    return
  }

  loading.value = true
  try {
    if (editId) {
      await api.put(`/tabs/${editId}`, form.value)
      notify.success('Tab updated!')
      router.push({ name: 'tab-detail', params: { id: editId } })
    } else {
      const { data } = await api.post('/tabs', form.value)
      notify.success('Tab uploaded!')
      router.push({ name: 'tab-detail', params: { id: data.tab.id } })
    }
  } catch (e) {
    handleApiValidationError(e, fieldErrors)
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <section class="max-w-2xl">
    <h1 class="text-3xl font-bold mb-6">{{ editId ? 'Edit tab' : 'Upload tab' }}</h1>
    <form class="space-y-4" @submit.prevent="submit">
      <FormField label="Title" :error="fieldErrors.title">
        <input v-model="form.title" type="text" class="input" @input="clearError('title')" />
      </FormField>
      <FormField label="Artist" :error="fieldErrors.artist">
        <input v-model="form.artist" type="text" class="input" @input="clearError('artist')" />
      </FormField>
      <FormField label="Tab content" :error="fieldErrors.content" hint="At least 10 characters.">
        <textarea
          v-model="form.content"
          rows="14"
          class="input font-mono text-sm"
          @input="clearError('content')"
        />
      </FormField>
      <button type="submit" class="btn-primary px-6" :disabled="loading">
        {{ loading ? 'Saving…' : editId ? 'Update' : 'Upload' }}
      </button>
    </form>
  </section>
</template>

<style scoped>
@reference "../style.css";

.input {
  @apply w-full px-4 py-2.5 rounded-lg border border-ink/15 bg-surface focus:outline-none focus:ring-2 focus:ring-amber/40;
}

.btn-primary {
  @apply py-2.5 rounded-lg bg-amber text-white font-medium hover:bg-amber-dark transition disabled:opacity-60;
}
</style>
