<script setup>
import AppNav from '../components/AppNav.vue'
</script>

<template>
  <div class="min-h-screen flex flex-col">
    <AppNav />
    <main class="flex-1 w-full max-w-5xl mx-auto px-4 py-8">
      <RouterView />
    </main>
    <footer class="border-t border-ink/10 py-6 text-center text-sm text-muted">
      Guitar Tabs — share chords &amp; tabs
    </footer>
  </div>
</template>
