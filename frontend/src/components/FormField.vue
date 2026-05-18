<script setup>
defineProps({
  label: { type: String, required: true },
  error: { type: String, default: '' },
  hint: { type: String, default: '' },
})
</script>

<template>
  <label class="field" :class="{ 'has-error': error }">
    <span>{{ label }}</span>
    <slot />
    <p v-if="error" class="field-error">{{ error }}</p>
    <p v-else-if="hint" class="field-hint">{{ hint }}</p>
  </label>
</template>

<style scoped>
@reference "../style.css";

.field {
  @apply block mb-4;
}

.field span {
  @apply block text-sm font-medium mb-1;
}

.field.has-error :deep(.input) {
  @apply border-red-400 ring-red-200;
}

.field-error {
  @apply mt-1 text-xs text-red-700;
}

.field-hint {
  @apply mt-1 text-xs text-muted;
}
</style>
