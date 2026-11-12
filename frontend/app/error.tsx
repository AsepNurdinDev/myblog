'use client';

import { useEffect } from 'react';

type ErrorPageProps = {
  error: Error & { digest?: string };
  reset: () => void;
};

export default function ErrorPage({ error, reset }: ErrorPageProps) {
  useEffect(() => {
    // Keep diagnostic details in the browser/server console, not in the UI.
    console.error('AsepBlog route error:', error);
  }, [error]);

  return (
    <main className="mx-auto max-w-3xl px-4 py-20 text-center">
      <h1 className="text-2xl font-bold text-stone-900">Terjadi kesalahan</h1>
      <p className="mt-2 text-sm text-stone-600">
        Konten belum dapat dimuat. Silakan coba beberapa saat lagi.
      </p>
      <button
        type="button"
        onClick={() => reset()}
        className="mt-6 rounded-xl bg-stone-900 px-5 py-2.5 text-xs font-semibold text-white hover:bg-stone-700"
      >
        Coba lagi
      </button>
    </main>
  );
}
