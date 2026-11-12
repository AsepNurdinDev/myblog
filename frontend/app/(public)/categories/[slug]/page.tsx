import type { Metadata } from 'next';
import { notFound } from 'next/navigation';
import { ApiError } from '@/lib/api/client';
import { getCategory, getCategories } from '@/lib/api/resources';
import { PostCard } from '@/components/articles/post-card';
import { Pagination } from '@/components/ui/pagination';
import { Suspense } from 'react';
import { RouteLoading } from '@/components/ui/route-loading';
type Props = { params: Promise<{ slug: string }>; searchParams: Promise<{ page?: string }> };
async function load(slug: string, page: number) {
  try {
    return await getCategory(slug, page);
  } catch (error) {
    if (error instanceof ApiError && error.status === 404) notFound();
    throw error;
  }
}
export async function generateMetadata({ params }: Props): Promise<Metadata> {
  const { slug } = await params;
  const data = await load(slug, 1);
  return {
    title: data.category.name,
    description: data.category.description || `Artikel dalam kategori ${data.category.name}.`,
    alternates: { canonical: `/categories/${slug}` },
  };
}
export default function CategoryPage({ params, searchParams }: Props) {
  return (
    <Suspense fallback={<RouteLoading variant="listing" />}>
      <CategoryPageContent params={params} searchParams={searchParams} />
    </Suspense>
  );
}

async function CategoryPageContent({ params, searchParams }: Props) {
  const [{ slug }, { page: pageValue }] = await Promise.all([params, searchParams]);
  const page = Math.max(1, Number(pageValue) || 1);
  const [result, categories] = await Promise.all([load(slug, page), getCategories()]);
  return (
    <div className="mx-auto max-w-3xl px-4 py-12 sm:px-6 sm:py-16">
      <header className="mb-10 space-y-2">
        <p className="text-xs font-semibold tracking-wide text-amber-700 uppercase">Kategori</p>
        <h1 className="text-2xl font-bold text-stone-900 sm:text-3xl">{result.category.name}</h1>
        {result.category.description && (
          <p className="text-sm text-stone-600">{result.category.description}</p>
        )}
      </header>
      <section className="mb-10">
        <form action="/articles" method="GET" className="relative max-w-xl">
          <input
            name="search"
            placeholder="Cari artikel, topik, atau kata kunci..."
            className="search-input py-3 text-sm"
          />
          <button className="absolute right-1.5 rounded-lg bg-stone-900 px-4 py-1.5 text-xs font-semibold text-white hover:bg-stone-700">
            Cari
          </button>
        </form>
      </section>
      {categories.length > 0 && (
        <section className="mt-16">
          <h2 className="mb-4 text-2xl font-extrabold tracking-tight text-stone-900">
            Topik & Kategori
          </h2>
          <div className="flex flex-wrap gap-2">
            {categories.map((category) => (
              <a
                key={category.id}
                href={`/categories/${category.slug}`}
                className="rounded-lg border border-stone-200/80 bg-white px-3.5 py-1.5 text-xs font-semibold text-stone-700 transition hover:border-stone-400 hover:bg-stone-50 hover:text-stone-900"
              >
                {category.name}
              </a>
            ))}
          </div>
        </section>
      )}
      <div className="mt-12 grid grid-cols-1 gap-6 sm:grid-cols-2">
        {result.data.length ? (
          result.data.map((post) => <PostCard key={post.id} post={post} />)
        ) : (
          <p className="col-span-full text-sm text-stone-500">
            Belum ada artikel yang diterbitkan dalam kategori ini.
          </p>
        )}
      </div>
      <div className="mt-10">
        <Pagination
          current={result.meta.current_page}
          last={result.meta.last_page}
          base={`/categories/${slug}`}
        />
      </div>
    </div>
  );
}
