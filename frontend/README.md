# MiniERP Frontend

React application built with Vite and Node.js 24.

## Run with Docker

From the repository root, copy the frontend environment template if needed:

```powershell
if (-not (Test-Path .\frontend\.env)) {
    Copy-Item .\frontend\.env.example .\frontend\.env
}
docker compose up --build
```

Open <http://localhost:5173>. `VITE_API_URL` is exposed to browser code, so do
not put secrets in frontend environment variables.

## Structure

- `src/assets/` — imported images, fonts, and other assets.
- `src/components/` — reusable UI components.
- `src/pages/` — page-level views.
- `src/services/` — API clients and other external service integrations.
- `public/` — static files served as-is.
