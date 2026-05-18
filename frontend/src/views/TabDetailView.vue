<script setup>
import { computed, onMounted, ref } from 'vue'
import { RouterLink, useRoute, useRouter } from 'vue-router'
import api from '../api/client'
import TabComments from '../components/TabComments.vue'
import { useAuthStore } from '../stores/auth'

const route = useRoute()
const router = useRouter()
const auth = useAuthStore()

const tab = ref(null)
const comments = ref([])
const loading = ref(true)
const isFavorite = ref(false)
const error = ref('')

const canEdit = computed(() => {
  if (!auth.isAuthenticated || !tab.value) return false
  return auth.user.id === tab.value.user_id || auth.isAdmin
})

onMounted(load)

async function load() {
  loading.value = true
  error.value = ''
  try {
    const { data } = await api.get(`/tabs/${route.params.id}`)
    tab.value = data.tab
    comments.value = data.comments ?? []
    if (auth.isAuthenticated) {
      const fav = await api.get('/favorites')
      isFavorite.value = fav.data.favorites.some((t) => t.id === tab.value.id)
    }
  } catch {
    error.value = 'Tab not found.'
  } finally {
    loading.value = false
  }
}

async function toggleFavorite() {
  if (!auth.isAuthenticated) {
    router.push({ name: 'login', query: { redirect: route.fullPath } })
    return
  }
  if (isFavorite.value) {
    await api.delete(`/favorites/${tab.value.id}`)
    isFavorite.value = false
  } else {
    await api.post(`/favorites/${tab.value.id}`)
    isFavorite.value = true
  }
}

async function removeTab() {
  if (!confirm('Delete this tab?')) return
  await api.delete(`/tabs/${tab.value.id}`)
  router.push({ name: 'home' })
}
</script>

<template>
  <section>
    <p v-if="loading" class="text-muted">Loading…</p>
    <p v-else-if="error" class="text-red-700">{{ error }}</p>
    <article v-else>
      <div class="flex flex-wrap items-start justify-between gap-4 mb-6">
        <div>
          <h1 class="text-3xl font-bold">{{ tab.title }}</h1>
          <p class="text-xl text-muted mt-1">{{ tab.artist }}</p>
          <p v-if="tab.user" class="text-sm text-muted mt-2">Uploaded by {{ tab.user.name }}</p>
        </div>
        <div class="flex flex-wrap gap-2">
          <button v-if="auth.isAuthenticated" type="button" class="btn-secondary" @click="toggleFavorite">
            {{ isFavorite ? '★ Saved' : '☆ Save favorite' }}
          </button>
          <RouterLink v-if="canEdit" :to="{ name: 'upload', query: { edit: tab.id } }" class="btn-secondary">
            Edit
          </RouterLink>
          <button v-if="canEdit" type="button" class="btn-danger" @click="removeTab">Delete</button>
        </div>
      </div>
      <pre class="tab-content">{{ tab.content }}</pre>
      <TabComments
        :tab-id="tab.id"
        :initial-comments="comments"
        @comments-updated="comments = $event"
      />
    </article>
  </section>
</template>

<style scoped>
@reference "../style.css";

.btn-secondary {
  @apply px-4 py-2 rounded-lg border border-ink/15 hover:bg-ink/5 transition text-sm font-medium;
}

.btn-danger {
  @apply px-4 py-2 rounded-lg bg-red-600 text-white hover:bg-red-700 transition text-sm font-medium;
}
</style>
