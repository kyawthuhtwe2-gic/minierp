import { useState } from 'react'
import { login } from '../../services/api'
import './LoginPage.css'

export default function LoginPage({ onLoggedIn }) {
  const [email, setEmail] = useState('')
  const [password, setPassword] = useState('')
  const [showPassword, setShowPassword] = useState(false)
  const [error, setError] = useState('')
  const [fieldErrors, setFieldErrors] = useState({})
  const [loading, setLoading] = useState(false)

  async function handleSubmit(event) {
    event.preventDefault()
    setError('')
    setFieldErrors({})
    setLoading(true)
    try {
      const data = await login(email.trim(), password)
      onLoggedIn(data.access_token, data.user)
    } catch (err) {
      setError(err.message)
      setFieldErrors(err.errors || {})
      setPassword('')
    } finally {
      setLoading(false)
    }
  }

  return (
    <main className="login">
      <form className="login-card" onSubmit={handleSubmit} noValidate>
        <h1>MiniERP</h1>
        <p className="login-sub">Sign in to your account</p>

        {error && (
          <div className="login-error" role="alert">
            {error}
          </div>
        )}

        <label htmlFor="email">Email</label>
        <input
          id="email"
          type="email"
          autoComplete="username"
          autoFocus
          required
          value={email}
          onChange={(e) => setEmail(e.target.value)}
          aria-invalid={Boolean(fieldErrors.email)}
        />
        {fieldErrors.email && <small>{fieldErrors.email[0]}</small>}

        <label htmlFor="password">Password</label>
        <div className="login-password">
          <input
            id="password"
            type={showPassword ? 'text' : 'password'}
            autoComplete="current-password"
            required
            value={password}
            onChange={(e) => setPassword(e.target.value)}
            aria-invalid={Boolean(fieldErrors.password)}
          />
          <button
            type="button"
            className="login-toggle"
            onClick={() => setShowPassword((s) => !s)}
          >
            {showPassword ? 'Hide' : 'Show'}
          </button>
        </div>
        {fieldErrors.password && <small>{fieldErrors.password[0]}</small>}

        <button type="submit" className="login-submit" disabled={loading}>
          {loading ? 'Signing in…' : 'Sign in'}
        </button>
      </form>
    </main>
  )
}
