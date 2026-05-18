<script setup>
import { RouterLink } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const auth = useAuthStore()
</script>

<template>
  <header class="border-b border-ink/10 bg-surface/80 backdrop-blur sticky top-0 z-10">
    <div class="max-w-5xl mx-auto px-4 py-4 flex items-center justify-between gap-4">
      <RouterLink to="/" class="text-xl font-bold tracking-tight text-amber-dark hover:text-amber transition">
        Guitar Tabs
      </RouterLink>
      <nav class="flex flex-wrap items-center gap-3 text-sm font-medium">
        <RouterLink to="/" class="nav-link">Home</RouterLink>
        <RouterLink to="/search" class="nav-link">Search</RouterLink>
        <RouterLink v-if="auth.isAuthenticated" to="/upload" class="nav-link">Upload</RouterLink>
        <RouterLink v-if="auth.isAuthenticated" to="/profile" class="nav-link">Profile</RouterLink>
        <RouterLink v-if="auth.isAdmin" to="/admin" class="nav-link">Admin</RouterLink>
        <template v-if="auth.isAuthenticated">
          <span class="text-muted hidden sm:inline">{{ auth.user?.name }}</span>
          <button type="button" class="btn-secondary" @click="auth.logout()">Logout</button>
        </template>
        <template v-else>
          <RouterLink to="/login" class="btn-secondary">Login</RouterLink>
          <RouterLink to="/register" class="btn-primary">Register</RouterLink>
        </template>
      </nav>
    </div>
  </header>
</template>

<style scoped>
@reference "../style.css";

.nav-link {
  @apply text-muted hover:text-ink transition;
}

.router-link-active.nav-link {
  @apply text-amber-dark;
}

.btn-primary {
  @apply px-3 py-1.5 rounded-lg bg-amber text-white hover:bg-amber-dark transition;
}

.btn-secondary {
  @apply px-3 py-1.5 rounded-lg border border-ink/15 hover:bg-ink/5 transition;
}
</style>
