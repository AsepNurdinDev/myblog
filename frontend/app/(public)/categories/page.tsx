import Link from 'next/link';
import { getCategories } from '@/lib/api/resources';
import { Suspense } from 'react';
import { RouteLoading } from '@/components/ui/route-loading';
export const metadata = {
  title: 'Kategori',
  description: 'Jelajahi artikel berdasarkan kategori.',
};
export default function CategoriesPage() {
  return (
    <Suspense fallback={<RouteLoading variant="listing" />}>
      <CategoriesPageContent />
    </Suspense>
  );
}

async function CategoriesPageContent() {
  const categories = await getCategories();
  return (
    <div className="mx-auto max-w-3xl px-4 py-12 sm:px-6 sm:py-16">
      <header className="mb-10 space-y-2">
        <p className="text-xs font-semibold tracking-wide text-amber-700 uppercase">Jelajahi</p>
        <h1 className="text-2xl font-bold text-stone-900 sm:text-3xl">Topik & Kategori</h1>
        <p className="text-sm text-stone-600">
          Pilih kategori untuk melihat kumpulan artikel terkait.
        </p>
      </header>
      <div className="grid grid-cols-1 gap-3 sm:grid-cols-2">
        {categories.map((category) => (
          <Link
            key={category.id}
            href={`/categories/${category.slug}`}
            className="rounded-xl border border-stone-200/80 bg-white p-5 transition hover:border-stone-400 hover:shadow-sm"
          >
            <h2 className="text-sm font-bold text-stone-900">{category.name}</h2>
            {category.description && (
              <p className="mt-1 text-xs text-stone-500">{category.description}</p>
            )}
            <p className="mt-3 text-[11px] font-semibold text-stone-400">
              {category.posts_count ?? 0} artikel →
            </p>
          </Link>
        ))}
      </div>
      {categories.length === 0 && <p className="text-sm text-stone-500">Belum ada kategori.</p>}
    </div>
  );
}
