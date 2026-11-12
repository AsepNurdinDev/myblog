import Link from 'next/link';
import { getCategories, getPosts } from '@/lib/api/resources';
import { PostCard } from '@/components/articles/post-card';
import { SearchForm } from '@/components/ui/search-form';
import { Pagination } from '@/components/ui/pagination';
import { EmptyState } from '@/components/ui/empty-state';
import { Suspense } from 'react';
import { RouteLoading } from '@/components/ui/route-loading';
export const metadata = {
  title: 'Catatan',
  description: 'Kumpulan catatan, tutorial, dan pemikiran seputar programming.',
};
type Props = {
  searchParams: Promise<{ page?: string; search?: string; category?: string; tag?: string }>;
};
export default function ArticlesPage({ searchParams }: Props) {
  return (
    <Suspense fallback={<RouteLoading variant="listing" />}>
      <ArticlesPageContent searchParams={searchParams} />
    </Suspense>
  );
}

async function ArticlesPageContent({ searchParams }: Props) {
  const params = await searchParams;
  const page = Math.max(1, Number(params.page) || 1);
  const [posts, categories] = await Promise.all([
    getPosts({
      page,
      search: params.search,
      category: params.category,
      tag: params.tag,
      per_page: 18,
    }),
    getCategories(),
  ]);
  const query = new URLSearchParams();
  if (params.search) query.set('search', params.search);
  if (params.category) query.set('category', params.category);
  if (params.tag) query.set('tag', params.tag);
  return (
    <div className="mx-auto max-w-5xl px-4 py-8 font-sans sm:px-6 sm:py-16">
      <header className="mb-6 space-y-1.5 border-b border-stone-200 pb-6 sm:mb-8 sm:space-y-2 sm:pb-8">
        <div className="flex items-center gap-2">
          <span className="inline-block h-2.5 w-2.5 animate-pulse rounded-full bg-emerald-500" />
          <span className="text-xs font-semibold tracking-wider text-stone-500 uppercase">
            Journal & Articles
          </span>
        </div>
        <h1 className="text-2xl font-extrabold tracking-tight text-stone-900 sm:text-4xl">
          Catatan & Artikel
        </h1>
        <p className="max-w-2xl text-xs leading-relaxed text-stone-600 sm:text-sm">
          Kumpulan catatan, tutorial, dan pemikiran seputar programming.
        </p>
      </header>
      <section className="mb-8">
        <SearchForm value={params.search || ''} />
        {params.search && (
          <p className="mt-2 text-xs text-stone-500">
            Menampilkan hasil pencarian untuk:{' '}
            <span className="font-semibold text-stone-800">&quot;{params.search}&quot;</span>
          </p>
        )}
      </section>
      {categories.length > 0 && (
        <section className="mb-10">
          <h2 className="mb-3 text-lg font-extrabold tracking-tight text-stone-900 sm:text-2xl">
            Topik & Kategori
          </h2>
          <div className="flex flex-wrap gap-1.5 sm:gap-2">
            {categories.map((category) => (
              <Link
                key={category.id}
                href={`/categories/${category.slug}`}
                className={`rounded-lg border px-3 py-1 text-xs font-semibold transition ${params.category === category.slug ? 'border-stone-400 bg-stone-100 text-stone-900' : 'border-stone-200/80 bg-white text-stone-700 hover:border-stone-400 hover:bg-stone-50'}`}
              >
                {category.name}
              </Link>
            ))}
          </div>
        </section>
      )}
      <section className="grid grid-cols-1 gap-4 sm:grid-cols-2 sm:gap-6 lg:grid-cols-3">
        {posts.data.length ? (
          posts.data.map((post) => <PostCard key={post.id} post={post} />)
        ) : (
          <EmptyState>
            {params.search
              ? 'Tidak ada artikel yang cocok dengan pencarian Anda.'
              : 'Belum ada artikel yang diterbitkan.'}
          </EmptyState>
        )}
      </section>
      <div className="mt-10 flex flex-col items-center gap-4">
        <Pagination
          current={posts.meta.current_page}
          last={posts.meta.last_page}
          base="/articles"
          query={query.toString()}
        />
      </div>
    </div>
  );
}
