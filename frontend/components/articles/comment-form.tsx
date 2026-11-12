'use client';
import { useState, type FormEvent } from 'react';
import { ApiError } from '@/lib/api/client';
import { submitComment } from '@/lib/api/resources';
export function CommentForm({ slug }: { slug: string }) {
  const [name, setName] = useState('');
  const [email, setEmail] = useState('');
  const [content, setContent] = useState('');
  const [website, setWebsite] = useState('');
  const [loading, setLoading] = useState(false);
  const [message, setMessage] = useState('');
  const [errors, setErrors] = useState<Record<string, string[]>>({});
  const [failed, setFailed] = useState(false);
  async function handleSubmit(event: FormEvent<HTMLFormElement>) {
    event.preventDefault();
    setMessage('');
    setErrors({});
    setFailed(false);
    if (name.trim().length === 0 || name.length > 255) {
      setErrors({ name: ['Nama wajib diisi dan maksimal 255 karakter.'] });
      return;
    }
    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email) || email.length > 255) {
      setErrors({ email: ['Masukkan alamat email yang valid.'] });
      return;
    }
    if (content.trim().length === 0 || content.length > 5000) {
      setErrors({ content: ['Komentar wajib diisi dan maksimal 5000 karakter.'] });
      return;
    }
    setLoading(true);
    try {
      const result = await submitComment(slug, { name, email, content, website });
      setMessage(result.message || 'Terima kasih! Komentar Anda menunggu persetujuan admin.');
      setName('');
      setEmail('');
      setContent('');
      setWebsite('');
    } catch (error) {
      setFailed(true);
      if (error instanceof ApiError) {
        setErrors(error.errors || {});
        setMessage(
          error.status === 429
            ? `Terlalu banyak komentar. Coba lagi${error.retryAfter ? ` dalam ${error.retryAfter} detik` : ' beberapa saat lagi'}.`
            : error.message,
        );
      } else setMessage('Terjadi kesalahan. Silakan coba kembali.');
    } finally {
      setLoading(false);
    }
  }
  return (
    <div className="mt-12 rounded-2xl border border-stone-200/80 bg-white p-6 shadow-sm sm:p-8">
      <h3 className="mb-4 text-base font-bold text-stone-900">Tinggalkan Komentar</h3>
      <form onSubmit={handleSubmit} className="space-y-4" noValidate>
        <div className="grid grid-cols-1 gap-4 sm:grid-cols-2">
          <div>
            <label htmlFor="comment-name" className="block text-xs font-semibold text-stone-700">
              Nama
            </label>
            <input
              id="comment-name"
              value={name}
              onChange={(e) => setName(e.target.value)}
              required
              maxLength={255}
              placeholder="Nama Anda"
              className="form-field mt-1"
            />
            {errors.name?.map((e) => (
              <p key={e} className="mt-1 text-xs text-red-600">
                {e}
              </p>
            ))}
          </div>
          <div>
            <label htmlFor="comment-email" className="block text-xs font-semibold text-stone-700">
              Email
            </label>
            <input
              id="comment-email"
              type="email"
              value={email}
              onChange={(e) => setEmail(e.target.value)}
              required
              maxLength={255}
              placeholder="email@domain.com"
              className="form-field mt-1"
            />
            <p className="mt-1 text-[11px] text-stone-400">
              Email tidak akan ditampilkan ke publik.
            </p>
            {errors.email?.map((e) => (
              <p key={e} className="mt-1 text-xs text-red-600">
                {e}
              </p>
            ))}
          </div>
        </div>
        <div>
          <label htmlFor="comment-content" className="block text-xs font-semibold text-stone-700">
            Komentar
          </label>
          <textarea
            id="comment-content"
            value={content}
            onChange={(e) => setContent(e.target.value)}
            rows={4}
            required
            maxLength={5000}
            placeholder="Tulis komentar atau tanggapan Anda di sini..."
            className="form-field mt-1"
          />
          {errors.content?.map((e) => (
            <p key={e} className="mt-1 text-xs text-red-600">
              {e}
            </p>
          ))}
        </div>
        <div className="hp-field" aria-hidden="true">
          <label htmlFor="comment-website">Website</label>
          <input
            id="comment-website"
            name="website"
            tabIndex={-1}
            autoComplete="off"
            value={website}
            onChange={(e) => setWebsite(e.target.value)}
          />
        </div>
        {message && (
          <p
            role="status"
            className={`rounded-xl border p-4 text-xs font-medium ${failed ? 'border-red-200 bg-red-50 text-red-800' : 'border-emerald-200 bg-emerald-50/80 text-emerald-800'}`}
          >
            {message}
          </p>
        )}
        <button
          type="submit"
          disabled={loading}
          className="inline-flex items-center rounded-xl bg-stone-900 px-6 py-2.5 text-xs font-semibold text-white shadow-sm transition hover:bg-stone-700 disabled:cursor-not-allowed disabled:opacity-60"
        >
          {loading ? 'Mengirim...' : 'Kirim Komentar'}
        </button>
      </form>
    </div>
  );
}
