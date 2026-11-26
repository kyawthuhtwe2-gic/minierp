const API_URL = import.meta.env.VITE_API_URL || 'http://localhost:8000/api'
const TOKEN_KEY = 'minierp_token'

export const tokenStore = {
  get: () => sessionStorage.getItem(TOKEN_KEY),
  set: (token) => sessionStorage.setItem(TOKEN_KEY, token),
  clear: () => sessionStorage.removeItem(TOKEN_KEY),
}

export class ApiError extends Error {
  constructor(message, status, errors) {
    super(message)
    this.status = status
    this.errors = errors
  }
}

async function request(path, { method = 'GET', body, auth = true } = {}) {
  const headers = { Accept: 'application/json' }
  if (body) headers['Content-Type'] = 'application/json'
  const token = tokenStore.get()
  if (auth && token) headers.Authorization = `Bearer ${token}`

  let response
  try {
    response = await fetch(`${API_URL}${path}`, {
      method,
      headers,
      body: body ? JSON.stringify(body) : undefined,
    })
  } catch {
    throw new ApiError('Cannot reach the server. Try again.', 0)
  }

  const data = await response.json().catch(() => ({}))
  if (!response.ok) {
    const message =
      response.status === 429
        ? 'Too many attempts. Wait a minute and try again.'
        : data.message || 'Request failed.'
    throw new ApiError(message, response.status, data.errors)
  }
  return data
}

export const login = (email, password) =>
  request('/login', { method: 'POST', body: { email, password }, auth: false })

export const me = () => request('/me')

export const logout = () => request('/logout', { method: 'POST' })

// Probe an endpoint; true when the server allows the current user (200), false on 401/403.
export async function canAccess(path) {
  try {
    await request(path)
    return true
  } catch (err) {
    if (err instanceof ApiError && (err.status === 401 || err.status === 403)) return false
    return null
  }
}

export const getArea = (path) => request(path)
