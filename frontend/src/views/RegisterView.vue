<script setup>
import { reactive, ref } from 'vue'
import { RouterLink, useRouter } from 'vue-router'
import FormField from '../components/FormField.vue'
import { useAuthStore } from '../stores/auth'
import { useNotificationStore } from '../stores/notifications'
import { handleApiValidationError } from '../utils/apiErrors'
import { hasErrors, validateFields, validators } from '../utils/validation'

const auth = useAuthStore()
const notify = useNotificationStore()
const router = useRouter()

const form = ref({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
})
const fieldErrors = reactive({})

function clearError(field) {
  delete fieldErrors[field]
}

async function submit() {
  Object.keys(fieldErrors).forEach((k) => delete fieldErrors[k])

  const errors = validateFields(form.value, {
    name: validators.name,
    email: validators.email,
    password: validators.password,
    password_confirmation: (v, f) => validators.passwordConfirmation(v, f),
  })

  if (hasErrors(errors)) {
    Object.assign(fieldErrors, errors)
    notify.error('Please fix the errors before registering.')
    return
  }

  try {
    await auth.register(form.value)
    notify.success('Account created successfully!')
    router.push('/')
  } catch (e) {
    handleApiValidationError(e, fieldErrors)
  }
}
</script>

<template>
  <div class="min-h-[70vh] flex items-center justify-center px-4">
    <form class="w-full max-w-md p-8 rounded-2xl border border-ink/10 bg-surface shadow-sm" @submit.prevent="submit">
      <h1 class="text-2xl font-bold mb-6">Create account</h1>

      <FormField label="Name" :error="fieldErrors.name">
        <input
          v-model="form.name"
          type="text"
          class="input"
          autocomplete="name"
          @input="clearError('name')"
        />
      </FormField>

      <FormField label="Email" :error="fieldErrors.email">
        <input
          v-model="form.email"
          type="email"
          class="input"
          autocomplete="email"
          @input="clearError('email')"
        />
      </FormField>

      <FormField
        label="Password"
        :error="fieldErrors.password"
        hint="At least 8 characters with a letter and a number."
      >
        <input
          v-model="form.password"
          type="password"
          class="input"
          autocomplete="new-password"
          @input="clearError('password')"
        />
      </FormField>

      <FormField label="Confirm password" :error="fieldErrors.password_confirmation">
        <input
          v-model="form.password_confirmation"
          type="password"
          class="input"
          autocomplete="new-password"
          @input="clearError('password_confirmation')"
        />
      </FormField>

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

.input {
  @apply w-full px-4 py-2.5 rounded-lg border border-ink/15 bg-cream focus:outline-none focus:ring-2 focus:ring-amber/40;
}

.btn-primary {
  @apply py-2.5 rounded-lg bg-amber text-white font-medium hover:bg-amber-dark transition disabled:opacity-60;
}
</style>

