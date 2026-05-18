const EMAIL_RE = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
const NAME_RE = /^[a-zA-Z0-9\s\-_.']{2,50}$/

export const validators = {
  name: (value) => {
    const v = (value ?? '').trim()
    if (!v) return 'Name is required.'
    if (v.length < 2) return 'Name must be at least 2 characters.'
    if (v.length > 50) return 'Name must be at most 50 characters.'
    if (!NAME_RE.test(v)) return 'Name may only contain letters, numbers, spaces, and - _ . \''
    return null
  },

  email: (value) => {
    const v = (value ?? '').trim()
    if (!v) return 'Email is required.'
    if (v.length > 255) return 'Email must be at most 255 characters.'
    if (!EMAIL_RE.test(v)) return 'Enter a valid email address.'
    return null
  },

  password: (value) => {
    const v = value ?? ''
    if (!v) return 'Password is required.'
    if (v.length < 8) return 'Password must be at least 8 characters.'
    if (v.length > 128) return 'Password must be at most 128 characters.'
    if (!/[a-zA-Z]/.test(v)) return 'Password must contain at least one letter.'
    if (!/[0-9]/.test(v)) return 'Password must contain at least one number.'
    return null
  },

  passwordConfirmation: (value, form) => {
    const msg = validators.password(value)
    if (msg && !value) return 'Please confirm your password.'
    if (form?.password !== value) return 'Passwords do not match.'
    return null
  },

  title: (value) => {
    const v = (value ?? '').trim()
    if (!v) return 'Title is required.'
    if (v.length > 255) return 'Title must be at most 255 characters.'
    return null
  },

  artist: (value) => {
    const v = (value ?? '').trim()
    if (!v) return 'Artist is required.'
    if (v.length > 255) return 'Artist must be at most 255 characters.'
    return null
  },

  content: (value) => {
    const v = (value ?? '').trim()
    if (!v) return 'Tab content is required.'
    if (v.length < 10) return 'Tab content must be at least 10 characters.'
    return null
  },

  comment: (value) => {
    const v = (value ?? '').trim()
    if (!v) return 'Comment cannot be empty.'
    if (v.length > 5000) return 'Comment must be at most 5000 characters.'
    return null
  },
}

export function validateFields(form, schema) {
  const errors = {}
  for (const [field, rule] of Object.entries(schema)) {
    const message = typeof rule === 'function' ? rule(form[field], form) : rule(form[field])
    if (message) errors[field] = message
  }
  return errors
}

export function hasErrors(errors) {
  return Object.keys(errors).length > 0
}
