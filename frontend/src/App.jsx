import { useEffect, useState } from 'react'
import AdminHome from './pages/admin/AdminHome'
import LoginPage from './pages/auth/LoginPage'
import UserHome from './pages/user/UserHome'
import { logout, me, tokenStore } from './services/api'

function App() {
  const [user, setUser] = useState(null)
  const [checking, setChecking] = useState(Boolean(tokenStore.get()))

  // Restore the session from a stored token; drop it if the API rejects it.
  useEffect(() => {
    if (!tokenStore.get()) return
    me()
      .then((data) => setUser(data.user))
      .catch(() => tokenStore.clear())
      .finally(() => setChecking(false))
  }, [])

  function handleLoggedIn(token, loggedInUser) {
    tokenStore.set(token)
    setUser(loggedInUser)
  }

  async function handleLogout() {
    try {
      await logout()
    } catch (err) {
      // 401 means the token is already invalid, so signing out locally is correct.
      // Any other failure is rethrown so the UI can show it and keep the session.
      if (err.status !== 401) throw err
    }
    tokenStore.clear()
    setUser(null)
  }

  if (checking) return null

  if (!user) return <LoginPage onLoggedIn={handleLoggedIn} />

  // Pick the UI area by role; the API still enforces permissions on every call.
  const Home = user.role === 'admin' ? AdminHome : UserHome
  return <Home user={user} onLogout={handleLogout} />

}

export default App
