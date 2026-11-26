import AccessGrid from '../../components/AccessGrid'
import AppShell from '../../components/AppShell'
import StaffDashboard from './StaffDashboard'

// Non-admin users: staff get the staff dashboard, other roles see what they may access.
export default function UserHome({ user, onLogout }) {
  return (
    <AppShell user={user} onLogout={onLogout}>
      {user.role === 'staff' ? <StaffDashboard /> : <AccessGrid user={user} />}
    </AppShell>
  )
}
