# Blade → Next.js migration notes

## Source UI retained

The public Blade layout was translated into React components while keeping its Figtree/Lora typography, stone color palette, sticky translucent navbar, max-width containers, article/project card proportions, responsive grids, footer columns, article typography, and profile/avatar presentation.

## API mapping

| Page                 | Laravel API                                                    |
| -------------------- | -------------------------------------------------------------- |
| `/`                  | `GET /home`                                                    |
| `/articles`          | `GET /posts` (`search`, `category`, `tag`, `page`, `per_page`) |
| `/articles/[slug]`   | `GET /posts/{slug}`, `POST /posts/{slug}/comments`             |
| `/categories`        | `GET /categories`                                              |
| `/categories/[slug]` | `GET /categories/{slug}`                                       |
| `/tags`              | `GET /tags`                                                    |
| `/tags/[slug]`       | `GET /tags/{slug}`                                             |
| `/projects`          | `GET /projects`                                                |
| `/projects/[slug]`   | `GET /projects/{slug}`                                         |
| `/about`             | `GET /profile`                                                 |

All requests are made through `lib/api/client.ts` and `lib/api/resources.ts`. They use `cache: 'no-store'` to avoid serving stale public content without a documented revalidation strategy.

## Known differences imposed by the existing API contract

- The old Blade project list has a search form, but `GET /projects` does not accept a search parameter. The frontend does not simulate a misleading partial search or change the Laravel contract; the project search field is omitted until the API supports it.
- The Blade post card estimates reading time from the full post body. The list API returns only summary fields and no `reading_time`, so cards omit that value. Article detail uses the API-provided `reading_time`.
- The Blade post-card implementation can estimate reading time using the loaded model content; the public API does not expose body content in list results. No extra backend field was added.
- The public profile API intentionally omits the owner's email. The About page therefore retains the collaboration callout but does not render the old `mailto:` button.
- The old Blade code blocks use Prism.js for syntax highlighting. The Next.js package does not include Prism.js and package installation was blocked by the unavailable npm registry in this environment. Code blocks retain their dark presentation and copy action, but syntax coloring is not yet included.
- The category and tag index routes are added because the API provides index endpoints, although the original public Blade routes only exposed category/tag detail pages.

## Security

- Article text, project content, and profile bio are sanitized by the SSR-safe allowlist in `lib/sanitization/html.ts` before `dangerouslySetInnerHTML`.
- Script/style/iframe/object/embed/form/SVG/math/template elements are removed; only allowlisted tags and attributes remain. Event handlers, inline styles, and unsafe URL schemes are not retained.
- External profile/project links are restricted to HTTP(S); external links opened in a new tab use `noopener noreferrer`.
- Public API comments are rendered as text, never as HTML. Email addresses are never rendered.
- Comment submission sends only `name`, `email`, `content`, and the empty `website` honeypot field. It does not send a status or post ID.

## Environment

Copy `.env.example` to `.env.local` and set `NEXT_PUBLIC_API_BASE_URL` to the Laravel API V1 base URL. For the current local setup use `http://127.0.0.1:8001/api/v1` (port 8000 belongs to another service). Production uses `https://api.asepblog.online/api/v1`. Laravel media URLs must also use the correct public origin: local `APP_URL` should match the browser-reachable Laravel origin, and production `APP_URL` must be `https://api.asepblog.online`. Remote media is rendered directly by the browser (`unoptimized`) so the Next.js container does not need outbound access to fetch image sources.

## Validation limitations

The supplied frontend archive has no `node_modules`. Installing pnpm 10.33.0 through Corepack failed because `registry.npmjs.org` could not be resolved in the working environment. Consequently, the actual Next.js ESLint, TypeScript, and production build commands could not run. A temporary ambient-stub structural TypeScript pass completed, but it is not a substitute for the real project typecheck. The Laravel API was also not running at `127.0.0.1:8000`, so endpoint smoke tests and browser visual tests remain outstanding.

- Blade's optional `/articles?all=1` unpaginated mode is not reproduced: the API contract exposes paginated results only (maximum `per_page` 50). The migrated list uses 18 items per page to match the old default page size.

A focused sanitizer smoke test was run against script tags, iframe tags, event-handler attributes, `javascript:` URLs, and `_blank` link rel handling; all checks passed.
