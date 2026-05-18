<script setup>
import { onMounted, reactive, ref, watch } from 'vue'
import { RouterLink, useRouter } from 'vue-router'
import api from '../api/client'
import FormField from './FormField.vue'
import { useAuthStore } from '../stores/auth'
import { useNotificationStore } from '../stores/notifications'
import { handleApiValidationError } from '../utils/apiErrors'
import { hasErrors, validateFields, validators } from '../utils/validation'

const props = defineProps({
  tabId: { type: [String, Number], required: true },
  initialComments: { type: Array, default: () => [] },
})

const emit = defineEmits(['comments-updated'])

const router = useRouter()
const auth = useAuthStore()

const comments = ref([])
const newComment = ref('')
const loading = ref(true)
const submitting = ref(false)
const fieldErrors = reactive({})
const notify = useNotificationStore()

function sortComments(list) {
  return [...list].sort((a, b) => {
    if (b.upvotes !== a.upvotes) return b.upvotes - a.upvotes
    if (a.downvotes !== b.downvotes) return a.downvotes - b.downvotes
    return new Date(b.created_at) - new Date(a.created_at)
  })
}

function setComments(list) {
  comments.value = sortComments(list)
  emit('comments-updated', comments.value)
}

async function loadComments() {
  loading.value = true
  try {
    const { data } = await api.get(`/tabs/${props.tabId}/comments`)
    setComments(data.comments)
  } finally {
    loading.value = false
  }
}

async function submitComment() {
  if (!auth.isAuthenticated) {
    router.push({ name: 'login', query: { redirect: router.currentRoute.value.fullPath } })
    return
  }
  delete fieldErrors.body

  const errors = validateFields({ body: newComment.value }, { body: validators.comment })
  if (hasErrors(errors)) {
    Object.assign(fieldErrors, errors)
    notify.error(errors.body)
    return
  }

  submitting.value = true
  try {
    const { data } = await api.post(`/tabs/${props.tabId}/comments`, {
      body: newComment.value.trim(),
    })
    setComments([...comments.value, data.comment])
    newComment.value = ''
    notify.success('Comment posted!')
  } catch (e) {
    handleApiValidationError(e, fieldErrors)
  } finally {
    submitting.value = false
  }
}

async function vote(comment, value) {
  if (!auth.isAuthenticated) {
    router.push({ name: 'login', query: { redirect: router.currentRoute.value.fullPath } })
    return
  }
  const { data } = await api.post(`/comments/${comment.id}/vote`, { value })
  const idx = comments.value.findIndex((c) => c.id === comment.id)
  if (idx !== -1) {
    const updated = [...comments.value]
    updated[idx] = data.comment
    setComments(updated)
  }
}

function canDelete(comment) {
  if (!auth.isAuthenticated) return false
  return auth.user.id === comment.user?.id || auth.isAdmin
}

async function removeComment(comment) {
  if (!confirm('Delete this comment?')) return
  await api.delete(`/comments/${comment.id}`)
  setComments(comments.value.filter((c) => c.id !== comment.id))
}

function formatDate(iso) {
  return new Date(iso).toLocaleString()
}

onMounted(() => {
  if (props.initialComments?.length) {
    setComments(props.initialComments)
    loading.value = false
  } else {
    loadComments()
  }
})

watch(() => props.tabId, () => loadComments())

watch(
  () => props.initialComments,
  (next) => {
    if (next?.length && !comments.value.length) {
      setComments(next)
    }
  },
)
</script>

<template>
  <section class="mt-12 pt-8 border-t border-ink/10">
    <h2 class="text-xl font-semibold mb-6">Comments</h2>

    <form v-if="auth.isAuthenticated" class="mb-8" @submit.prevent="submitComment">
      <FormField label="Add a comment" :error="fieldErrors.body">
        <textarea
          v-model="newComment"
          rows="3"
          placeholder="Share a tip or correction…"
          class="input w-full"
          maxlength="5000"
          @input="delete fieldErrors.body"
        />
      </FormField>
      <button type="submit" class="btn-primary" :disabled="submitting || !newComment.trim()">
        {{ submitting ? 'Posting…' : 'Post comment' }}
      </button>
    </form>
    <p v-else class="text-sm text-muted mb-8">
      <RouterLink to="/login" class="text-amber-dark hover:underline">Log in</RouterLink>
      to leave a comment.
    </p>

    <p v-if="loading" class="text-muted">Loading comments…</p>
    <p v-else-if="!comments.length" class="text-muted">No comments yet. Be the first!</p>

    <ul v-else class="space-y-4">
      <li
        v-for="comment in comments"
        :key="comment.id"
        class="p-4 rounded-xl border border-ink/10 bg-surface"
      >
        <div class="flex items-start justify-between gap-3 mb-2">
          <div>
            <span class="font-medium">{{ comment.user?.name }}</span>
            <span class="text-xs text-muted ml-2">{{ formatDate(comment.created_at) }}</span>
          </div>
          <button
            v-if="canDelete(comment)"
            type="button"
            class="text-xs text-red-700 hover:underline shrink-0"
            @click="removeComment(comment)"
          >
            Delete
          </button>
        </div>
        <p class="text-sm whitespace-pre-wrap mb-3">{{ comment.body }}</p>
        <div class="flex items-center gap-2">
          <button
            type="button"
            class="vote-btn"
            :class="{ active: comment.user_vote === 1 }"
            @click="vote(comment, 1)"
          >
            ▲ {{ comment.upvotes }}
          </button>
          <button
            type="button"
            class="vote-btn"
            :class="{ active: comment.user_vote === -1 }"
            @click="vote(comment, -1)"
          >
            ▼ {{ comment.downvotes }}
          </button>
        </div>
      </li>
    </ul>
  </section>
</template>

<style scoped>
@reference "../style.css";

.input {
  @apply px-4 py-2.5 rounded-lg border border-ink/15 bg-cream focus:outline-none focus:ring-2 focus:ring-amber/40 text-sm;
}

.btn-primary {
  @apply px-4 py-2 rounded-lg bg-amber text-white text-sm font-medium hover:bg-amber-dark transition disabled:opacity-60;
}

.vote-btn {
  @apply px-2.5 py-1 rounded-md border border-ink/15 text-sm text-muted hover:bg-ink/5 transition;
}

.vote-btn.active {
  @apply border-amber bg-amber/10 text-amber-dark font-medium;
}
</style>

