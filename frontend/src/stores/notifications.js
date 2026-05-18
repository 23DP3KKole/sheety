import { defineStore } from 'pinia'
import { ref } from 'vue'

let nextId = 0

export const useNotificationStore = defineStore('notifications', () => {
  const items = ref([])

  function push(message, type = 'error', duration = 5000) {
    const id = ++nextId
    items.value.push({ id, message, type })
    if (duration > 0) {
      setTimeout(() => dismiss(id), duration)
    }
    return id
  }

  function dismiss(id) {
    items.value = items.value.filter((n) => n.id !== id)
  }

  function error(message) {
    return push(message, 'error')
  }

  function success(message) {
    return push(message, 'success')
  }

  return { items, push, dismiss, error, success }
})
