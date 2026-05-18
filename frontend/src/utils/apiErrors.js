import { useNotificationStore } from '../stores/notifications'

export function handleApiValidationError(error, fieldErrorsRef) {
  const notify = useNotificationStore()
  const data = error.response?.data

  if (error.response?.status === 422 && data?.errors) {
    const mapped = {}
    for (const [field, messages] of Object.entries(data.errors)) {
      mapped[field] = Array.isArray(messages) ? messages[0] : messages
    }
    if (fieldErrorsRef) {
      for (const key of Object.keys(fieldErrorsRef)) {
        delete fieldErrorsRef[key]
      }
      Object.assign(fieldErrorsRef, mapped)
    }
    notify.error(data.message || 'Please fix the highlighted fields.')
    return true
  }

  notify.error(data?.message || 'Something went wrong. Please try again.')
  return false
}
