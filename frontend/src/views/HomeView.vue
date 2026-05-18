<script setup>
import { onMounted, ref } from 'vue'
import api from '../api/client'
import TabCard from '../components/TabCard.vue'

const tabs = ref([])
const loading = ref(true)

onMounted(async () => {
  try {
    const { data } = await api.get('/tabs')
    tabs.value = data.data ?? data
  } finally {
    loading.value = false
  }
})
</script>

<template>
  <section>
    <div class="mb-8">
      <h1 class="text-3xl font-bold">Latest guitar tabs</h1>
      <p class="text-muted mt-2">Browse community-shared chords and tablature.</p>
    </div>

    <p v-if="loading" class="text-muted">Loading tabs…</p>
    <div v-else-if="tabs.length" class="grid gap-4 sm:grid-cols-2">
      <TabCard v-for="tab in tabs" :key="tab.id" :tab="tab" />
    </div>
    <p v-else class="text-muted">No tabs yet. Be the first to upload one!</p>
  </section>
</template>
