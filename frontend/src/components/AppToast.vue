<script setup>
import { useNotificationStore } from '../stores/notifications'

const notifications = useNotificationStore()
</script>

<template>
  <!-- toast root -->
  <div class="toast-container" aria-live="polite">
    <TransitionGroup name="toast">
      <div
        v-for="item in notifications.items"
        :key="item.id"
        class="toast"
        :class="item.type"
        role="alert"
      >
        <p>{{ item.message }}</p>
        <button type="button" class="toast-close" aria-label="Dismiss" @click="notifications.dismiss(item.id)">
          &times;
        </button>
      </div>
    </TransitionGroup>
  </div>
</template>

<style scoped>
@reference "../style.css";

.toast-container {
  @apply fixed top-4 right-4 z-50 flex flex-col gap-2 max-w-sm w-full pointer-events-none;
}

.toast {
  @apply pointer-events-auto flex items-start justify-between gap-3 px-4 py-3 rounded-lg shadow-lg border text-sm font-medium;
}

.toast.error {
  @apply bg-red-50 border-red-200 text-red-900;
}

.toast.success {
  @apply bg-green-50 border-green-200 text-green-900;
}

.toast-close {
  @apply text-lg leading-none opacity-60 hover:opacity-100 shrink-0;
}

.toast-enter-active,
.toast-leave-active {
  transition: all 0.25s ease;
}

.toast-enter-from,
.toast-leave-to {
  opacity: 0;
  transform: translateX(1rem);
}
</style>
