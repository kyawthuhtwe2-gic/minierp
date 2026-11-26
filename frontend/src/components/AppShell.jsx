import './AppShell.css'

function initials(name) {
  return (
    name
      .split(/\s+/)
      .filter(Boolean)
      .slice(0, 2)
      .map((part) => part[0].toUpperCase())
      .join('') || '?'
  )
}

// Shared layout: fixed header (brand, avatar, sign out). Page content goes in children.
export default function AppShell({ user, onLogout, children }) {
  return (
    <div className="home">
      <header className="home-bar">
        <span className="home-brand">MiniERP</span>
        <div className="home-bar-right">
          <span className="home-avatar" aria-hidden="true">
            {initials(user.name)}
          </span>
          <button type="button" className="home-signout" onClick={onLogout}>
            Sign out
          </button>
        </div>
      </header>

      <main className="home-main">{children}</main>
    </div>
  )
}
