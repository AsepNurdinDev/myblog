# AsepBlog Frontend

Next.js public frontend for AsepBlog. Laravel remains the source of truth and is consumed only through the public REST API V1.

## Requirements

- Node.js 22 or compatible with Next.js 16.4.5
- pnpm 10.33.0 (package manager pinned in `package.json`)
- Laravel API running locally for live content

## Setup

```bash
pnpm install
cp .env.example .env.local
```

Set `NEXT_PUBLIC_API_BASE_URL` in `.env.local`:

```dotenv
NEXT_PUBLIC_API_BASE_URL=http://127.0.0.1:8001/api/v1
```

For production, use `https://api.asepblog.online/api/v1`.

## Run

Start Laravel from the backend directory:

```bash
cd ../backend
composer install
cp .env.example .env
php artisan key:generate
php artisan storage:link
php artisan serve --host=127.0.0.1 --port=8001
```

In another terminal, start Next.js:

```bash
cd frontend
pnpm dev -- --port 3010
```

Open http://localhost:3010. Port 3000–3003 may already be used by other services on the homelab; choose another free port if 3010 is occupied.

## Validation

```bash
pnpm lint
pnpm typecheck
pnpm build
```

## Routes

- `/` homepage
- `/articles` and `/articles/[slug]`
- `/categories` and `/categories/[slug]`
- `/tags` and `/tags/[slug]`
- `/projects` and `/projects/[slug]`
- `/about`

All page content is fetched from Laravel API V1. Public pages use uncached API requests to avoid stale published content. Rich HTML fields are sanitized through a server-safe allowlist before rendering.

See [migration notes](docs/MIGRATION-NOTES.md) for API mappings, security handling, and known gaps versus the original Blade behavior.
