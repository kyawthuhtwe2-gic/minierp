// Welcome text shown inside the admin and user pages (not part of the header).
export default function WelcomeHero({ user }) {
  return (
    <section className="home-hero">
      <p className="home-eyebrow">Welcome back</p>
      <h1>{user.name}</h1>
      <p className="home-meta">
        {user.email}
        <span className="home-badge">{user.role ?? 'no role'}</span>
      </p>
    </section>
  )
}
