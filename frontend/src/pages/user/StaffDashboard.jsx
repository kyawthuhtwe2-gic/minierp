import { useEffect, useState } from 'react'
import { getArea } from '../../services/api'

const TASKS = [
  { title: 'My tasks', desc: 'Work assigned to you today' },
  { title: 'Orders', desc: 'Process and track customer orders' },
  { title: 'Inventory', desc: 'Check stock levels' },
]

// Rendered only for staff, but access is enforced by the API (role read from DB).
export default function StaffDashboard() {
  const [state, setState] = useState({ loading: true })

  useEffect(() => {
    let active = true
    getArea('/staff/dashboard')
      .then((data) => active && setState({ message: data.message }))
      .catch((err) => active && setState({ error: err.message }))
    return () => {
      active = false
    }
  }, [])

  if (state.error) {
    return (
      <p className="home-card denied" role="alert">
        {state.error}
      </p>
    )
  }

  return (
    <section aria-labelledby="staff-title">
      <div className="home-grid">
        {TASKS.map((t) => (
          <article key={t.title} className="home-card ok">
            <h3>{t.title}</h3>
            <p>{t.desc}</p>
          </article>
        ))}
      </div>
    </section>
  )
}
