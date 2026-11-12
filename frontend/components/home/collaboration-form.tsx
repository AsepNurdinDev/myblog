'use client';

import type { FormEvent } from 'react';

// Ganti dengan email kamu
const CONTACT_EMAIL = 'emailkamu@example.com';

// Format nomor: 628xxxxxxxxxx
const WHATSAPP_NUMBER = '628xxxxxxxxxx';

export function CollaborationForm() {
  function handleSubmit(event: FormEvent<HTMLFormElement>) {
    event.preventDefault();

    const formData = new FormData(event.currentTarget);

    const name = String(formData.get('name') || '');
    const email = String(formData.get('email') || '');
    const topic = String(formData.get('topic') || '');
    const message = String(formData.get('message') || '');

    const subject = `Kolaborasi: ${topic}`;
    const body = [
      `Nama: ${name}`,
      `Email: ${email}`,
      `Topik: ${topic}`,
      '',
      'Pesan:',
      message,
    ].join('\n');

    window.location.href = `mailto:${CONTACT_EMAIL}?subject=${encodeURIComponent(subject)}&body=${encodeURIComponent(body)}`;
  }

  const whatsappMessage = 'Halo, saya tertarik untuk berdiskusi mengenai peluang kolaborasi.';

  const whatsappUrl = `https://wa.me/${WHATSAPP_NUMBER}?text=${encodeURIComponent(whatsappMessage)}`;

  const inputClass =
    'w-full rounded-lg border border-stone-200 bg-stone-50/50 px-3.5 py-2.5 text-sm text-stone-800 outline-none transition placeholder:text-stone-400 focus:border-stone-400 focus:bg-white';

  return (
    <section className="mt-6 border-t border-stone-100 pt-6">
      <h2 className="mb-2 text-xs font-bold tracking-wider text-stone-400 uppercase">
        Mari Berkolaborasi
      </h2>

      <p className="mb-5 text-xs leading-relaxed text-stone-500">
        Punya ide proyek, ingin bekerja sama, atau sekadar ingin berdiskusi? Silakan kirim pesan
        melalui form berikut.
      </p>

      <form onSubmit={handleSubmit} className="space-y-4">
        <div className="grid grid-cols-1 gap-4 sm:grid-cols-2">
          <div>
            <label
              htmlFor="collab-name"
              className="mb-1.5 block text-xs font-semibold text-stone-700"
            >
              Nama
            </label>
            <input
              id="collab-name"
              name="name"
              type="text"
              required
              maxLength={100}
              placeholder="Nama lengkap"
              className={inputClass}
            />
          </div>

          <div>
            <label
              htmlFor="collab-email"
              className="mb-1.5 block text-xs font-semibold text-stone-700"
            >
              Email
            </label>
            <input
              id="collab-email"
              name="email"
              type="email"
              required
              maxLength={254}
              placeholder="nama@email.com"
              className={inputClass}
            />
          </div>
        </div>

        <div>
          <label
            htmlFor="collab-topic"
            className="mb-1.5 block text-xs font-semibold text-stone-700"
          >
            Topik kolaborasi
          </label>
          <select id="collab-topic" name="topic" required defaultValue="" className={inputClass}>
            <option value="" disabled>
              Pilih topik
            </option>
            <option value="Web Development">Web Development</option>
            <option value="Software Development">Software Development</option>
            <option value="DevOps & Infrastructure">DevOps & Infrastructure</option>
            <option value="Cybersecurity">Cybersecurity</option>
            <option value="Konsultasi Teknis">Konsultasi Teknis</option>
            <option value="Project Collaboration">Project Collaboration</option>
            <option value="Lainnya">Lainnya</option>
          </select>
        </div>

        <div>
          <label
            htmlFor="collab-message"
            className="mb-1.5 block text-xs font-semibold text-stone-700"
          >
            Pesan
          </label>
          <textarea
            id="collab-message"
            name="message"
            required
            minLength={10}
            maxLength={5000}
            rows={4}
            placeholder="Ceritakan ide atau kebutuhan kolaborasi kamu..."
            className={`${inputClass} resize-y`}
          />
        </div>

        <div className="flex flex-col gap-3 sm:flex-row">
          <button
            type="submit"
            className="inline-flex items-center justify-center gap-2 rounded-xl bg-stone-900 px-5 py-2.5 text-xs font-semibold text-white transition hover:bg-stone-700"
          >
            Kirim melalui Email <span>↗</span>
          </button>

          <a
            href={whatsappUrl}
            target="_blank"
            rel="noopener noreferrer"
            className="inline-flex items-center justify-center gap-2 rounded-xl border border-stone-200 bg-stone-50/50 px-5 py-2.5 text-xs font-semibold text-stone-700 transition hover:border-stone-400 hover:bg-stone-100"
          >
            <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor" aria-hidden="true">
              <path d="M12.04 2a9.85 9.85 0 0 0-8.46 14.9L2.2 22l5.24-1.37A9.9 9.9 0 1 0 12.04 2Zm0 18.1a8.2 8.2 0 0 1-4.18-1.14l-.3-.18-3.11.81.83-3.03-.2-.31A8.17 8.17 0 1 1 12.04 20.1Zm4.49-6.12c-.25-.13-.57-.28-.82-.41-.25-.13-.42-.2-.6.06-.17.26-.66.83-.81 1-.15.17-.3.2-.55.07-.25-.13-1.06-.39-2.02-1.25-.75-.67-1.26-1.5-1.41-1.75-.15-.26-.02-.4.11-.53.12-.12.25-.3.38-.45.13-.15.17-.26.26-.43.08-.17.04-.32-.02-.45-.06-.13-.57-1.37-.78-1.88-.2-.49-.41-.42-.57-.43h-.49c-.17 0-.45.06-.68.32-.23.26-.89.87-.89 2.11s.91 2.45 1.04 2.62c.13.17 1.79 2.73 4.33 3.83.6.19 1.16.16 1.6.1.49-.07 1.47-.6 1.68-1.18.21-.58.21-1.07.15-1.18-.06-.11-.23-.17-.49-.3Z" />
            </svg>
            Hubungi via WhatsApp
          </a>
        </div>

        <p className="text-[10px] leading-relaxed text-stone-400">
          Pengiriman email akan membuka aplikasi email perangkat kamu. WhatsApp akan membuka
          percakapan dengan pesan awal otomatis.
        </p>
      </form>
    </section>
  );
}
