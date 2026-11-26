import UserMenu from './UserMenu'
import './AppShell.css'

// Shared layout: fixed header (brand, user menu). Page content goes in children.
export default function AppShell({ user, onLogout, children }) {
  return (
    <div className="home">
      <header className="home-bar">
        <span className="home-brand">MiniERP</span>
        <UserMenu user={user} onLogout={onLogout} />
      </header>

      <main className="home-main">{children}</main>
    </div>
  )
}
