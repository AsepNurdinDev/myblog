import Link from 'next/link';
import { getTags } from '@/lib/api/resources';
import { Suspense } from 'react';
import { RouteLoading } from '@/components/ui/route-loading';
export const metadata = { title: 'Tag', description: 'Jelajahi artikel berdasarkan tag.' };
export default function TagsPage() {
  return (
    <Suspense fallback={<RouteLoading variant="listing" />}>
      <TagsPageContent />
    </Suspense>
  );
}

async function TagsPageContent() {
  const tags = await getTags();
  return (
    <div className="mx-auto max-w-3xl px-4 py-12 sm:px-6 sm:py-16">
      <header className="mb-10 space-y-2">
        <p className="text-xs font-semibold tracking-wide text-amber-700 uppercase">Jelajahi</p>
        <h1 className="text-2xl font-bold text-stone-900 sm:text-3xl">Tag Artikel</h1>
      </header>
      <div className="flex flex-wrap gap-2">
        {tags.map((tag) => (
          <Link
            key={tag.id}
            href={`/tags/${tag.slug}`}
            className="rounded-lg border border-stone-200/80 bg-white px-3.5 py-2 text-xs font-semibold text-stone-700 transition hover:border-stone-400 hover:bg-stone-50"
          >
            #{tag.name} <span className="text-stone-400">({tag.posts_count ?? 0})</span>
          </Link>
        ))}
      </div>
      {tags.length === 0 && <p className="text-sm text-stone-500">Belum ada tag.</p>}
    </div>
  );
}
