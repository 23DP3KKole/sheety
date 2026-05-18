<script setup>
import { reactive, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import FormField from '../components/FormField.vue'
import { useAuthStore } from '../stores/auth'
import { useNotificationStore } from '../stores/notifications'
import { handleApiValidationError } from '../utils/apiErrors'
import { hasErrors, validateFields, validators } from '../utils/validation'

const auth = useAuthStore()
const notify = useNotificationStore()
const router = useRouter()
const route = useRoute()

const form = ref({ email: '', password: '' })
const fieldErrors = reactive({})

function clearError(field) {
  delete fieldErrors[field]
}

async function submit() {
  for (const key of Object.keys(fieldErrors)) delete fieldErrors[key]

  const errors = validateFields(form.value, {
    email: validators.email,
    password: (v) => (v ? null : 'Password is required.'),
  })

  if (hasErrors(errors)) {
    Object.assign(fieldErrors, errors)
    notify.error('Please fix the errors before signing in.')
    return
  }

  try {
    await auth.login(form.value)
    notify.success('Welcome back!')
    router.push(route.query.redirect?.toString() || '/')
  } catch (e) {
    handleApiValidationError(e, fieldErrors)
  }
}
</script>

<template>
  <div class="min-h-[70vh] flex items-center justify-center px-4">
    <form class="w-full max-w-md p-8 rounded-2xl border border-ink/10 bg-surface shadow-sm" @submit.prevent="submit">
      <h1 class="text-2xl font-bold mb-6">Login</h1>

      <FormField label="Email" :error="fieldErrors.email">
        <input
          v-model="form.email"
          type="email"
          class="input"
          autocomplete="email"
          @input="clearError('email')"
        />
      </FormField>

      <FormField label="Password" :error="fieldErrors.password">
        <input
          v-model="form.password"
          type="password"
          class="input"
          autocomplete="current-password"
          @input="clearError('password')"
        />
      </FormField>

      <button type="submit" class="btn-primary w-full mt-2" :disabled="auth.loading">
        {{ auth.loading ? 'Signing in…' : 'Sign in' }}
      </button>
      <p class="text-sm text-muted mt-4 text-center">
        No account?
        <RouterLink to="/register" class="text-amber-dark hover:underline">Register</RouterLink>
      </p>
    </form>
  </div>
</template>

<style scoped>
@reference "../style.css";

.input {
  @apply w-full px-4 py-2.5 rounded-lg border border-ink/15 bg-cream focus:outline-none focus:ring-2 focus:ring-amber/40;
}

.btn-primary {
  @apply py-2.5 rounded-lg bg-amber text-white font-medium hover:bg-amber-dark transition disabled:opacity-60;
}
</style>
