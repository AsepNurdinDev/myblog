import Link from 'next/link';
import type { Post } from '@/types/api';
import { SmartImage } from '@/components/ui/smart-image';

export function PostCard({ post }: { post: Post }) {
  const articleUrl = `/articles/${post.slug}`;

  const formattedDate = post.published_at
    ? new Date(post.published_at).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
      })
    : null;

  return (
    <article className="group flex h-full flex-col overflow-hidden rounded-2xl border border-stone-200/80 bg-white transition-all duration-300 hover:-translate-y-1 hover:border-stone-300 hover:shadow-xl hover:shadow-stone-200/50">
      {/* =====================================================
          COVER IMAGE
      ===================================================== */}
      <Link
        href={articleUrl}
        aria-label={`Baca artikel: ${post.title}`}
        className="relative block aspect-[16/10] w-full shrink-0 overflow-hidden bg-stone-100"
      >
        {post.cover_image ? (
          <SmartImage
            src={post.cover_image}
            alt={post.title}
            fill
            sizes="(max-width: 640px) 100vw, (max-width: 1024px) 50vw, 33vw"
            className="object-cover transition-transform duration-700 ease-out group-hover:scale-105"
          />
        ) : (
          <div className="absolute inset-0 flex items-center justify-center bg-stone-100">
            <div className="flex h-16 w-16 items-center justify-center rounded-2xl border border-stone-200 bg-white text-2xl font-bold text-stone-400 shadow-sm transition-transform duration-500 group-hover:scale-110">
              A.
            </div>
          </div>
        )}

        {/* Image Overlay */}
        <div className="absolute inset-0 bg-gradient-to-t from-black/20 via-transparent to-transparent opacity-60" />

        {/* Category Badge */}
        {post.category && (
          <span className="absolute top-4 left-4 inline-flex items-center rounded-full border border-white/20 bg-white/90 px-3 py-1.5 text-[11px] font-semibold tracking-wide text-stone-800 shadow-sm backdrop-blur-md">
            {post.category.name}
          </span>
        )}
      </Link>

      {/* =====================================================
          CARD CONTENT
      ===================================================== */}
      <div className="flex flex-1 flex-col p-5 sm:p-6">
        {/* Category for articles without cover */}
        {!post.cover_image && post.category && (
          <div className="mb-3">
            <Link
              href={`/categories/${post.category.slug}`}
              className="inline-flex items-center rounded-full bg-stone-100 px-3 py-1.5 text-[11px] font-semibold text-stone-700 transition hover:bg-stone-200"
            >
              {post.category.name}
            </Link>
          </div>
        )}

        {/* Published Date */}
        {formattedDate && (
          <div className="mb-3 flex items-center gap-2 text-xs text-stone-400">
            <time dateTime={post.published_at || undefined}>{formattedDate}</time>

            <span aria-hidden="true" className="text-stone-300">
              ·
            </span>

            {post.reading_time && <span>{post.reading_time} menit baca</span>}
          </div>
        )}

        {/* Article Title */}
        <h3 className="line-clamp-3 text-lg leading-snug font-bold tracking-tight text-stone-900 transition-colors duration-200 group-hover:text-stone-600 sm:text-xl">
          <Link
            href={articleUrl}
            className="focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-stone-500"
          >
            {post.title}
          </Link>
        </h3>

        {/* Tags */}
        {post.tags.length > 0 && (
          <div className="mt-4 flex flex-wrap gap-x-2 gap-y-1.5">
            {post.tags.slice(0, 3).map((tag) => (
              <Link
                key={tag.id}
                href={`/tags/${tag.slug}`}
                className="text-[11px] font-medium text-stone-400 transition-colors hover:text-stone-900"
              >
                #{tag.name}
              </Link>
            ))}

            {post.tags.length > 3 && (
              <span className="text-[11px] text-stone-400">+{post.tags.length - 3}</span>
            )}
          </div>
        )}

        {/* =====================================================
            CARD FOOTER
        ===================================================== */}
        <div className="mt-5 mt-auto flex items-center justify-between gap-3 border-t border-stone-100 pt-5">
          <Link
            href={articleUrl}
            className="inline-flex items-center gap-2 text-xs font-semibold text-stone-900 transition-all duration-200 hover:gap-3 hover:text-stone-500 sm:text-sm"
          >
            Baca Artikel
            <span
              aria-hidden="true"
              className="transition-transform duration-200 group-hover:translate-x-1"
            >
              →
            </span>
          </Link>

          {post.reading_time && (
            <span className="shrink-0 text-[11px] text-stone-400 sm:text-xs">
              {post.reading_time} menit membaca
            </span>
          )}
        </div>
      </div>
    </article>
  );
}
