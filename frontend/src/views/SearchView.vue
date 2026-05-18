<script setup>
import { ref, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import api from '../api/client'
import TabCard from '../components/TabCard.vue'

const route = useRoute()
const router = useRouter()
const query = ref(route.query.q?.toString() ?? '')
const tabs = ref([])
const loading = ref(false)

async function search() {
  loading.value = true
  try {
    const { data } = await api.get('/tabs', { params: { search: query.value || undefined } })
    tabs.value = data.data ?? data
  } finally {
    loading.value = false
  }
}

function submit() {
  router.replace({ query: query.value ? { q: query.value } : {} })
  search()
}

watch(() => route.query.q, (q) => {
  query.value = q?.toString() ?? ''
  search()
}, { immediate: true })
</script>

<template>
  <section>
    <h1 class="text-3xl font-bold mb-6">Search tabs</h1>
    <form class="flex gap-2 mb-8" @submit.prevent="submit">
      <input
        v-model="query"
        type="search"
        placeholder="Song title or artist…"
        class="input flex-1"
      />
      <button type="submit" class="btn-primary px-5">Search</button>
    </form>

    <p v-if="loading" class="text-muted">Searching…</p>
    <div v-else-if="tabs.length" class="grid gap-4 sm:grid-cols-2">
      <TabCard v-for="tab in tabs" :key="tab.id" :tab="tab" />
    </div>
    <p v-else class="text-muted">No tabs found.</p>
  </section>
</template>

<style scoped>
@reference "../style.css";

.input {
  @apply w-full px-4 py-2.5 rounded-lg border border-ink/15 bg-surface focus:outline-none focus:ring-2 focus:ring-amber/40;
}

.btn-primary {
  @apply rounded-lg bg-amber text-white font-medium hover:bg-amber-dark transition;
}
</style>
