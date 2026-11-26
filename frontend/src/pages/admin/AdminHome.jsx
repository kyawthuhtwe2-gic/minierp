import AccessGrid from '../../components/AccessGrid'
import AppShell from '../../components/AppShell'

export default function AdminHome({ user, onLogout }) {
  return (
    <AppShell user={user} onLogout={onLogout}>
      <AccessGrid user={user} />
    </AppShell>
  )
}
