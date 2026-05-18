<script setup>
import { RouterLink } from 'vue-router'

defineProps({
  tab: { type: Object, required: true },
})
</script>

<template>
  <RouterLink
    :to="{ name: 'tab-detail', params: { id: tab.id } }"
    class="block p-5 rounded-xl border border-ink/10 bg-surface hover:border-amber/40 hover:shadow-md transition group"
  >
    <h3 class="text-lg font-semibold group-hover:text-amber-dark transition">{{ tab.title }}</h3>
    <p class="text-muted mt-1">{{ tab.artist }}</p>
    <p v-if="tab.user" class="text-xs text-muted/80 mt-3">by {{ tab.user.name }}</p>
  </RouterLink>
</template>
