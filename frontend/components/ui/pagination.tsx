import Link from 'next/link';
export function Pagination({
  current,
  last,
  base,
  query = '',
}: {
  current: number;
  last: number;
  base: string;
  query?: string;
}) {
  if (last <= 1) return null;
  const href = (page: number) => `${base}?page=${page}${query ? `&${query}` : ''}`;
  const pages = Array.from({ length: last }, (_, i) => i + 1).filter(
    (page) => page === 1 || page === last || Math.abs(page - current) <= 1,
  );
  return (
    <nav aria-label="Pagination" className="mt-10 flex flex-wrap items-center justify-center gap-2">
      <Link
        aria-disabled={current <= 1}
        className={`rounded-lg border px-3 py-2 text-xs font-semibold ${current <= 1 ? 'pointer-events-none border-stone-100 text-stone-300' : 'border-stone-200 bg-white text-stone-700 hover:bg-stone-50'}`}
        href={href(Math.max(1, current - 1))}
      >
        Sebelumnya
      </Link>
      {pages.map((page, i) => (
        <span key={page} className="contents">
          {i > 0 && page - pages[i - 1] > 1 && <span className="px-1 text-stone-400">…</span>}
          <Link
            href={href(page)}
            aria-current={page === current ? 'page' : undefined}
            className={`rounded-lg border px-3 py-2 text-xs font-semibold ${page === current ? 'border-stone-900 bg-stone-900 text-white' : 'border-stone-200 bg-white text-stone-700 hover:bg-stone-50'}`}
          >
            {page}
          </Link>
        </span>
      ))}
      <Link
        aria-disabled={current >= last}
        className={`rounded-lg border px-3 py-2 text-xs font-semibold ${current >= last ? 'pointer-events-none border-stone-100 text-stone-300' : 'border-stone-200 bg-white text-stone-700 hover:bg-stone-50'}`}
        href={href(Math.min(last, current + 1))}
      >
        Berikutnya
      </Link>
    </nav>
  );
}
