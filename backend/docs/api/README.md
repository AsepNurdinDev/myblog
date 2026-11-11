# MyBlog Public REST API (v1)

Laravel REST API consumed by the Next.js frontend.

| Domain | Role |
|---|---|
| `asepblog.online` | Next.js public frontend |
| `api.asepblog.online` | This Laravel API (`/api/v1/...`) |
| `admin.asepblog.online` | Filament admin panel (unchanged) |

Laravel stays the single source of truth. Next.js talks to it only over HTTP and never touches MySQL.

## Architecture

- Routes: `routes/api.php` (registered in `bootstrap/app.php`; the `api` prefix is automatic, so URLs are `/api/v1/...`).
- Controllers: `app/Http/Controllers/Api/V1/` (separate from the Blade controllers, which are untouched).
- Resources: `app/Http/Resources/Api/V1/` (all JSON goes through API Resources).
- Comment validation: `app/Http/Requests/Api/V1/StorePostCommentRequest.php` extends the existing `StoreCommentRequest` (same rules) and adds a link-count check.
- Media URLs: `app/Support/Media.php`.
- Rate limiters: `AppServiceProvider::configureRateLimiting()`.
- No authentication, no Sanctum, no sessions, no CSRF on `/api/*`. Only one write endpoint exists (comment submission); nothing exposes admin operations.
- Visibility rules mirror the Blade site: only `status = published` posts are public; **all projects are public** (the `ProjectStatus` enum has no hidden state); only `approved` comments are shown.

## Endpoints

Base: `{base_url}/api/v1` (local: `http://localhost:8000/api/v1`).

| Method | Path | Description |
|---|---|---|
| GET | `/home` | Profile, latest 6 published posts, latest 3 projects, all categories |
| GET | `/posts` | Paginated published posts |
| GET | `/posts/{slug}` | Full post (404 if draft) |
| POST | `/posts/{slug}/comments` | Submit a comment (stored as `pending`) |
| GET | `/categories` | All categories |
| GET | `/categories/{slug}` | Category + paginated published posts |
| GET | `/tags` | All tags |
| GET | `/tags/{slug}` | Tag + paginated published posts |
| GET | `/projects` | Paginated projects |
| GET | `/projects/{slug}` | Project details + skills |
| GET | `/profile` | Public profile + skills |

### Query parameters

| Endpoint | Parameter | Notes |
|---|---|---|
| `/posts` | `search` | Matches title or excerpt (`%` and `_` are treated literally) |
| `/posts` | `category` | Category slug |
| `/posts` | `tag` | Tag slug |
| `/posts`, `/categories/{slug}`, `/tags/{slug}`, `/projects` | `per_page` | 1-50. Defaults: posts 12, category/tag 10, projects 9. Invalid values fall back to the default |
| paginated endpoints | `page` | Page number |

Posts are sorted by `published_at` descending; projects by `started_at` descending.

### Pagination format

Standard Laravel pagination:

```json
{
  "data": [ ... ],
  "links": { "first": "...", "last": "...", "prev": null, "next": "..." },
  "meta": { "current_page": 1, "from": 1, "last_page": 3, "path": "...", "per_page": 12, "to": 12, "total": 30, "links": [ ... ] }
}
```

`/categories/{slug}` adds a top-level `"category": {...}` and `/tags/{slug}` adds `"tag": {...}` next to `data/links/meta`.

### Examples

`GET /api/v1/posts/{slug}`

```json
{
  "data": {
    "id": 1,
    "title": "Example Article",
    "slug": "example-article",
    "excerpt": "Article excerpt",
    "cover_image": "https://api.asepblog.online/storage/posts/covers/abc.png",
    "published_at": "2026-01-01T10:00:00Z",
    "category": { "id": 1, "name": "Tutorial", "slug": "tutorial", "description": null },
    "tags": [ { "id": 1, "name": "Laravel", "slug": "laravel" } ],
    "content": [
      { "type": "text",  "data": { "content": "<p>HTML from the rich editor</p>" } },
      { "type": "image", "data": { "url": "https://api.asepblog.online/storage/posts/content/x.png", "caption": "Caption" } },
      { "type": "code",  "data": { "language": "php", "filename": "web.php", "code": "<?php ..." } },
      { "type": "quote", "data": { "quote": "Text", "author": "Someone" } }
    ],
    "reading_time": 3,
    "seo": { "title": "SEO Title", "description": "SEO Description" },
    "comments": [ { "id": 5, "name": "Alice", "content": "Nice!", "created_at": "2026-01-02T08:00:00Z" } ]
  }
}
```

`seo.title` falls back to the post title and `seo.description` to the excerpt. Text blocks contain HTML written by the admin in Filament; sanitize or render it appropriately in Next.js.

`GET /api/v1/projects/{slug}`: `id, title, slug, description, content (HTML), thumbnail, url, github_url, status (ongoing|completed|archived), started_at, completed_at, skills[]`.

`GET /api/v1/profile`: `name, headline, bio (HTML), bio_text (plain), location, avatar, website, github, linkedin, instagram, twitter, skills[]`. Only the owner's display name is exposed, never email or account data.

`POST /api/v1/posts/{slug}/comments`

```json
{ "name": "Jane Visitor", "email": "jane@example.com", "content": "Great article!" }
```

`201 Created`:

```json
{
  "message": "Thank you! Your comment has been submitted and will appear after it is approved.",
  "status": "pending",
  "data": { "id": 12, "name": "Jane Visitor", "content": "Great article!", "created_at": "2026-01-02T08:00:00Z" }
}
```

## Comment submission workflow

1. Visitor posts name, email and content (no login).
2. The post is looked up by slug and must be published, otherwise `404`.
3. Validation (same rules as the Blade form): `name` required, max 255; `email` required, valid, max 255; `content` required, max 5000. Extra rule: at most 3 links in the content. Failures return `422`.
4. The comment is stored with status `pending`. Any `status` or `post_id` sent by the client is ignored.
5. The admin approves it in Filament (Comments). Only then does it appear in `GET /posts/{slug}`.
6. The email is stored but never returned by any endpoint.

Spam protection:

- Rate limit: 5 submissions per minute and 30 per hour per IP (`429`).
- Honeypot: send nothing in the `website` field. If a bot fills it, the API answers `201` but stores nothing. The Next.js form should include a hidden `website` input that real users leave empty.
- Identical comment (same post, email, content) within 10 minutes is stored once (a double-click is harmless).
- Link limit (above).

All other endpoints share a general limit of 120 requests per minute per IP.

## Errors

| Status | Body |
|---|---|
| 404 | `{"message": "Resource not found."}` (unknown route, unknown slug, unpublished post) |
| 405 | Laravel default JSON message |
| 422 | `{"message": "...", "errors": {"field": ["..."]}}` |
| 429 | `{"message": "Too many requests. Please try again later.", "retry_after": 42}` plus `Retry-After` header |
| 500 | `{"message": "Server Error"}` when `APP_DEBUG=false`. **Set `APP_DEBUG=false` in production**, otherwise Laravel includes debug details |

`/api/*` always renders JSON, even without an `Accept` header.

## Media URLs

Filament uploads go to the `public` disk (`posts/covers`, `posts/content`, `profiles/avatars`, `projects/thumbnails`). The API returns absolute URLs built with `Storage::disk('public')->url()`, which uses `APP_URL`:

- Production `.env`: `APP_URL=https://api.asepblog.online`
- Local: `APP_URL=http://localhost:8000`

Run `php artisan storage:link` so `public/storage` points to `storage/app/public`. Nothing is hardcoded and existing files are untouched. Missing images are `null`. Add `api.asepblog.online` to `images.remotePatterns` in `next.config.js`.

## CORS

`config/cors.php` (applies to `api/*`): origins `http://localhost:3000` and `https://asepblog.online`; methods `GET, POST, OPTIONS`; headers `Content-Type, Accept, Origin`; `supports_credentials = false`; `Retry-After` and `X-RateLimit-*` are exposed. Add other origins (for example a Vercel preview URL) in that file.

## Local development

```bash
composer install
cp .env.example .env && php artisan key:generate
# configure DB_* in .env, then (only if the schema is not yet in your DB):
php artisan migrate
php artisan storage:link
php artisan serve        # http://localhost:8000
curl http://localhost:8000/api/v1/home
```

Tests (in-memory SQLite, never your real DB):

```bash
php artisan test --compact tests/Feature/Api
php artisan test
```

## Postman

1. Postman, then Import, and select `docs/api/postman/MyBlog-API.postman_collection.json` and `docs/api/postman/MyBlog-API.postman_environment.json`.
2. Choose the environment **MyBlog Local** (top right).
3. Variables: `base_url` (`http://localhost:8000`), `api_version` (`v1`). The `post_slug`, `category_slug`, `tag_slug`, `project_slug` variables are filled automatically by the list requests; run the lists first.
4. Each request has tests asserting the expected status code and response shape. Run the whole collection with the Collection Runner (the comment request is rate limited to 5/min).

## Insomnia

1. Application, then Import, and select `docs/api/insomnia/MyBlog-API.insomnia.json`.
2. The workspace **MyBlog API** has folders Homepage, Articles, Categories, Tags, Projects, Profile, Comments. Edit the **Base Environment** to change `base_url` / `api_version` or the slug variables.
3. Expected status codes are noted in each request description. The Insomnia format has no test scripts, so use Postman for automated checks.

## Dependencies

No new Composer or npm packages were added. Everything uses native Laravel features (API Resources, rate limiter, CORS middleware).
