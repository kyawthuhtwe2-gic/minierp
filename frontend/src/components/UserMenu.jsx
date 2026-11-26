import { useEffect, useRef, useState } from 'react'
import { LogoutIcon, MailIcon, ShieldIcon, UserIcon } from './Icons'

// Header person-icon button that opens a profile popup (name, email, role, Logout).
export default function UserMenu({ user, onLogout }) {
  const [open, setOpen] = useState(false)
  const [busy, setBusy] = useState(false)
  const [error, setError] = useState('')
  const ref = useRef(null)

  useEffect(() => {
    if (!open) return
    function onPointerDown(event) {
      if (ref.current && !ref.current.contains(event.target)) setOpen(false)
    }
    function onKeyDown(event) {
      if (event.key === 'Escape') setOpen(false)
    }
    document.addEventListener('pointerdown', onPointerDown)
    document.addEventListener('keydown', onKeyDown)
    return () => {
      document.removeEventListener('pointerdown', onPointerDown)
      document.removeEventListener('keydown', onKeyDown)
    }
  }, [open])

  async function handleLogout() {
    setError('')
    setBusy(true)
    try {
      await onLogout() // success unmounts this menu and shows the login page
    } catch (err) {
      setError(err.message || 'Logout failed. Try again.')
      setBusy(false)
    }
  }

  return (
    <div className="user-menu" ref={ref}>
      <button
        type="button"
        className="user-menu-trigger"
        aria-haspopup="true"
        aria-expanded={open}
        aria-label={`Account menu for ${user.name}`}
        title={user.name}
        onClick={() => setOpen((o) => !o)}
      >
        <UserIcon size={20} />
      </button>

      {open && (
        <div className="user-menu-popup" role="dialog" aria-label="User profile">
          <div className="user-menu-head">
            <span className="user-menu-person large">
              <UserIcon size={26} />
            </span>
          </div>

          <ul className="user-menu-details">
            <li>
              <UserIcon />
              <span>{user.name}</span>
            </li>
            <li>
              <MailIcon />
              <span>{user.email}</span>
            </li>
            {user.role && (
              <li>
                <ShieldIcon />
                <span>
                  Role: <strong className="user-menu-role">{user.role}</strong>
                </span>
              </li>
            )}
          </ul>

          {error && (
            <p className="user-menu-error" role="alert">
              {error}
            </p>
          )}

          <button type="button" className="user-menu-logout" onClick={handleLogout} disabled={busy}>
            <LogoutIcon />
            {busy ? 'Logging out…' : 'Logout'}
          </button>
        </div>
      )}
    </div>
  )
}
