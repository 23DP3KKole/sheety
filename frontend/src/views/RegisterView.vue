<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const auth = useAuthStore()
const router = useRouter()

const form = ref({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
})
const error = ref('')

async function submit() {
  error.value = ''
  try {
    await auth.register(form.value)
    router.push('/')
  } catch (e) {
    const errors = e.response?.data?.errors
    error.value = errors
      ? Object.values(errors).flat().join(' ')
      : e.response?.data?.message || 'Registration failed.'
  }
}
</script>

<template>
  <div class="min-h-[70vh] flex items-center justify-center px-4">
    <form class="w-full max-w-md p-8 rounded-2xl border border-ink/10 bg-surface shadow-sm" @submit.prevent="submit">
      <h1 class="text-2xl font-bold mb-6">Create account</h1>
      <p v-if="error" class="mb-4 text-sm text-red-700">{{ error }}</p>
      <label class="field">
        <span>Name</span>
        <input v-model="form.name" type="text" required class="input" />
      </label>
      <label class="field">
        <span>Email</span>
        <input v-model="form.email" type="email" required class="input" />
      </label>
      <label class="field">
        <span>Password</span>
        <input v-model="form.password" type="password" required minlength="8" class="input" />
      </label>
      <label class="field">
        <span>Confirm password</span>
        <input v-model="form.password_confirmation" type="password" required class="input" />
      </label>
      <button type="submit" class="btn-primary w-full mt-2" :disabled="auth.loading">
        {{ auth.loading ? 'Creating…' : 'Register' }}
      </button>
      <p class="text-sm text-muted mt-4 text-center">
        Already have an account?
        <RouterLink to="/login" class="text-amber-dark hover:underline">Login</RouterLink>
      </p>
    </form>
  </div>
</template>

<style scoped>
@reference "../style.css";

.field {
  @apply block mb-4;
}

.field span {
  @apply block text-sm font-medium mb-1;
}

.input {
  @apply w-full px-4 py-2.5 rounded-lg border border-ink/15 bg-cream focus:outline-none focus:ring-2 focus:ring-amber/40;
}

.btn-primary {
  @apply py-2.5 rounded-lg bg-amber text-white font-medium hover:bg-amber-dark transition disabled:opacity-60;
}
</style>
