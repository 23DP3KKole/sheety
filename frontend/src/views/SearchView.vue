<script setup>
import { ref, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import api from '../api/client'
import TabCard from '../components/TabCard.vue'

const route = useRoute()
const router = useRouter()

const query = ref(route.query.q?.toString() ?? '')
const sortBy = ref(route.query.sort?.toString() ?? 'created_at')
const sortDir = ref(route.query.dir?.toString() ?? 'desc')
const tabs = ref([])
const loading = ref(false)

const sortOptions = [
  { value: 'created_at', label: 'Date added' },
  { value: 'title', label: 'Title' },
  { value: 'artist', label: 'Artist' },
]

const dirOptions = [
  { value: 'asc', label: 'Ascending' },
  { value: 'desc', label: 'Descending' },
]

function buildQuery() {
  const q = {}
  if (query.value.trim()) q.q = query.value.trim()
  if (sortBy.value !== 'created_at') q.sort = sortBy.value
  if (sortDir.value !== 'desc') q.dir = sortDir.value
  return q
}

async function search() {
  loading.value = true
  try {
    const { data } = await api.get('/tabs', {
      params: {
        search: query.value.trim() || undefined,
        sort_by: sortBy.value,
        sort_dir: sortDir.value,
      },
    })
    tabs.value = data.data ?? data
  } finally {
    loading.value = false
  }
}

function applyFilters() {
  router.replace({ query: buildQuery() })
}

function submit() {
  applyFilters()
}

function onSortChange() {
  applyFilters()
}

watch(
  () => [route.query.q, route.query.sort, route.query.dir],
  ([q, sort, dir]) => {
    query.value = q?.toString() ?? ''
    sortBy.value = sort?.toString() ?? 'created_at'
    sortDir.value = dir?.toString() ?? 'desc'
    search()
  },
  { immediate: true },
)
</script>

<template>
  <section>
    <h1 class="text-3xl font-bold mb-6">Search tabs</h1>

    <form class="flex flex-col gap-4 mb-8" @submit.prevent="submit">
      <div class="flex gap-2">
        <input
          v-model="query"
          type="search"
          placeholder="Song title or artist…"
          class="input flex-1"
        />
        <button type="submit" class="btn-primary px-5">Search</button>
      </div>

      <div class="flex flex-wrap items-end gap-4">
        <label class="filter-field">
          <span>Sort by</span>
          <select v-model="sortBy" class="input select" @change="onSortChange">
            <option v-for="opt in sortOptions" :key="opt.value" :value="opt.value">
              {{ opt.label }}
            </option>
          </select>
        </label>
        <label class="filter-field">
          <span>Order</span>
          <select v-model="sortDir" class="input select" @change="onSortChange">
            <option v-for="opt in dirOptions" :key="opt.value" :value="opt.value">
              {{ opt.label }}
            </option>
          </select>
        </label>
      </div>
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

.select {
  @apply min-w-[10rem] cursor-pointer;
}

.filter-field {
  @apply block;
}

.filter-field span {
  @apply block text-sm font-medium text-muted mb-1;
}

.btn-primary {
  @apply rounded-lg bg-amber text-white font-medium hover:bg-amber-dark transition;
}
</style>
