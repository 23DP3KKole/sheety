<script setup>
import { onMounted, ref } from 'vue'
import api from '../api/client'
import TabCard from '../components/TabCard.vue'
import { useAuthStore } from '../stores/auth'

const auth = useAuthStore()
const favorites = ref([])
const myTabs = ref([])
const loading = ref(true)

onMounted(async () => {
  try {
    const [favRes, tabsRes] = await Promise.all([
      api.get('/favorites'),
      api.get('/tabs', { params: { search: '' } }),
    ])
    favorites.value = favRes.data.favorites
    const all = tabsRes.data.data ?? tabsRes.data
    myTabs.value = all.filter((t) => t.user_id === auth.user.id)
  } finally {
    loading.value = false
  }
})
</script>

<template>
  <section>
    <h1 class="text-3xl font-bold mb-2">Profile</h1>
    <p class="text-muted mb-8">{{ auth.user?.name }} · {{ auth.user?.email }}</p>

    <p v-if="loading" class="text-muted">Loading…</p>
    <template v-else>
      <h2 class="text-xl font-semibold mb-4">Favorites</h2>
      <div v-if="favorites.length" class="grid gap-4 sm:grid-cols-2 mb-10">
        <TabCard v-for="tab in favorites" :key="tab.id" :tab="tab" />
      </div>
      <p v-else class="text-muted mb-10">No favorites yet.</p>

      <h2 class="text-xl font-semibold mb-4">My tabs</h2>
      <div v-if="myTabs.length" class="grid gap-4 sm:grid-cols-2">
        <TabCard v-for="tab in myTabs" :key="tab.id" :tab="tab" />
      </div>
      <p v-else class="text-muted">You haven't uploaded any tabs.</p>
    </template>
  </section>
</template>
