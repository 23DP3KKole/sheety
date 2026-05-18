<script setup>
import { onMounted, ref } from 'vue'
import api from '../api/client'

const users = ref([])
const tabs = ref([])
const loading = ref(true)

onMounted(load)

async function load() {
  loading.value = true
  try {
    const [usersRes, tabsRes] = await Promise.all([
      api.get('/admin/users'),
      api.get('/admin/tabs'),
    ])
    users.value = usersRes.data.users
    tabs.value = tabsRes.data.tabs
  } finally {
    loading.value = false
  }
}

async function deleteUser(id) {
  if (!confirm('Delete this user and all their tabs?')) return
  await api.delete(`/admin/users/${id}`)
  users.value = users.value.filter((u) => u.id !== id)
  tabs.value = tabs.value.filter((t) => t.user_id !== id)
}

async function deleteTab(id) {
  if (!confirm('Delete this tab?')) return
  await api.delete(`/tabs/${id}`)
  tabs.value = tabs.value.filter((t) => t.id !== id)
}
</script>

<template>
  <section>
    <h1 class="text-3xl font-bold mb-8">Admin panel</h1>
    <p v-if="loading" class="text-muted">Loading…</p>
    <template v-else>
      <div class="mb-12">
        <h2 class="text-xl font-semibold mb-4">Users</h2>
        <div class="overflow-x-auto rounded-xl border border-ink/10">
          <table class="w-full text-sm">
            <thead class="bg-ink/5 text-left">
              <tr>
                <th class="p-3">Name</th>
                <th class="p-3">Email</th>
                <th class="p-3">Role</th>
                <th class="p-3"></th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="user in users" :key="user.id" class="border-t border-ink/10">
                <td class="p-3">{{ user.name }}</td>
                <td class="p-3">{{ user.email }}</td>
                <td class="p-3 capitalize">{{ user.role }}</td>
                <td class="p-3 text-right">
                  <button type="button" class="text-red-700 hover:underline" @click="deleteUser(user.id)">
                    Delete
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <div>
        <h2 class="text-xl font-semibold mb-4">Tabs</h2>
        <div class="overflow-x-auto rounded-xl border border-ink/10">
          <table class="w-full text-sm">
            <thead class="bg-ink/5 text-left">
              <tr>
                <th class="p-3">Title</th>
                <th class="p-3">Artist</th>
                <th class="p-3">Uploader</th>
                <th class="p-3"></th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="tab in tabs" :key="tab.id" class="border-t border-ink/10">
                <td class="p-3">{{ tab.title }}</td>
                <td class="p-3">{{ tab.artist }}</td>
                <td class="p-3">{{ tab.user?.name }}</td>
                <td class="p-3 text-right">
                  <button type="button" class="text-red-700 hover:underline" @click="deleteTab(tab.id)">
                    Delete
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </template>
  </section>
</template>

