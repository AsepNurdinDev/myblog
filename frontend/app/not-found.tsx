import Link from 'next/link';
import { PublicLayout } from '@/components/layout/public-layout';
export default function NotFound() {
  return (
    <PublicLayout>
      <main className="mx-auto max-w-3xl px-4 py-24 text-center">
        <p className="text-xs font-semibold tracking-wider text-stone-400 uppercase">
          404 — Not Found
        </p>
        <h1 className="mt-3 text-3xl font-extrabold tracking-tight text-stone-900">
          Halaman tidak ditemukan
        </h1>
        <p className="mt-3 text-sm text-stone-600">
          Halaman yang Anda cari mungkin telah dipindahkan atau tidak tersedia.
        </p>
        <Link
          href="/"
          className="mt-7 inline-flex rounded-xl bg-stone-900 px-5 py-2.5 text-xs font-semibold text-white hover:bg-stone-700"
        >
          Kembali ke Beranda
        </Link>
      </main>
    </PublicLayout>
  );
}
