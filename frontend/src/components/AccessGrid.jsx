import { useEffect, useState } from 'react'
import { canAccess } from '../services/api'

const AREAS = [
  { path: '/admin/dashboard', title: 'Admin dashboard', desc: 'System administration' },
  { path: '/management', title: 'Management', desc: 'Team and operations overview' },
  { path: '/staff/dashboard', title: 'Staff dashboard', desc: 'Daily work area' },
]

// Access is asked of the server (role read from DB), never derived from the client.
export default function AccessGrid({ user }) {
  const [access, setAccess] = useState({})

  useEffect(() => {
    let active = true
    Promise.all(AREAS.map((a) => canAccess(a.path))).then((results) => {
      if (active) setAccess(Object.fromEntries(AREAS.map((a, i) => [a.path, results[i]])))
    })
    return () => {
      active = false
    }
  }, [user])

  return (
    <section aria-labelledby="areas-title">
      <div className="home-grid">
        {AREAS.map((area) => {
          const state = access[area.path]
          const label =
            state === undefined ? 'Checking…' : state === true ? 'Allowed' : state === false ? 'No access' : 'Unavailable'
          const tone = state === true ? 'ok' : state === false ? 'denied' : 'pending'
          return (
            <article key={area.path} className={`home-card ${tone}`}>
              <span className="home-status">{label}</span>
              <h3>{area.title}</h3>
              <p>{area.desc}</p>
            </article>
          )
        })}
      </div>
    </section>
  )
}
