import type { Metadata } from 'next';
import { notFound } from 'next/navigation';
import { ApiError } from '@/lib/api/client';
import { getTag } from '@/lib/api/resources';
import { PostCard } from '@/components/articles/post-card';
import { Pagination } from '@/components/ui/pagination';
import { Suspense } from 'react';
import { RouteLoading } from '@/components/ui/route-loading';
type Props = { params: Promise<{ slug: string }>; searchParams: Promise<{ page?: string }> };
async function load(slug: string, page: number) {
  try {
    return await getTag(slug, page);
  } catch (error) {
    if (error instanceof ApiError && error.status === 404) notFound();
    throw error;
  }
}
export async function generateMetadata({ params }: Props): Promise<Metadata> {
  const { slug } = await params;
  const data = await load(slug, 1);
  return {
    title: `#${data.tag.name}`,
    description: `Artikel dengan tag ${data.tag.name}.`,
    alternates: { canonical: `/tags/${slug}` },
  };
}
export default function TagPage({ params, searchParams }: Props) {
  return (
    <Suspense fallback={<RouteLoading variant="listing" />}>
      <TagPageContent params={params} searchParams={searchParams} />
    </Suspense>
  );
}

async function TagPageContent({ params, searchParams }: Props) {
  const [{ slug }, { page: pageValue }] = await Promise.all([params, searchParams]);
  const page = Math.max(1, Number(pageValue) || 1);
  const result = await load(slug, page);
  return (
    <div className="mx-auto max-w-3xl px-4 py-12 sm:px-6 sm:py-16">
      <header className="mb-10 space-y-2">
        <p className="text-xs font-semibold tracking-wide text-amber-700 uppercase">Tag</p>
        <h1 className="text-2xl font-bold text-stone-900 sm:text-3xl">#{result.tag.name}</h1>
      </header>
      <div className="grid grid-cols-1 gap-6 sm:grid-cols-2">
        {result.data.length ? (
          result.data.map((post) => <PostCard key={post.id} post={post} />)
        ) : (
          <p className="col-span-full text-sm text-stone-500">
            Belum ada artikel yang diterbitkan dengan tag ini.
          </p>
        )}
      </div>
      <div className="mt-10">
        <Pagination
          current={result.meta.current_page}
          last={result.meta.last_page}
          base={`/tags/${slug}`}
        />
      </div>
    </div>
  );
}
