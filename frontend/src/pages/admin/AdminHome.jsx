import AccessGrid from '../../components/AccessGrid'
import AppShell from '../../components/AppShell'
import WelcomeHero from '../../components/WelcomeHero'

export default function AdminHome({ user, onLogout }) {
  return (
    <AppShell user={user} onLogout={onLogout}>
      <WelcomeHero user={user} />
      <AccessGrid user={user} />
    </AppShell>
  )
}
