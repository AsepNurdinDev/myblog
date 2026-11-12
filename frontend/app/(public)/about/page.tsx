import type { Metadata } from 'next';
import { getProfile } from '@/lib/api/resources';
import { sanitizeHtml } from '@/lib/sanitization/html';
import { AvatarLightbox } from '@/components/home/avatar-lightbox';
import { safeExternalUrl } from '@/lib/utils/url';
import { Suspense } from 'react';
import { CollaborationForm } from '@/components/home/collaboration-form';

import { RouteLoading } from '@/components/ui/route-loading';
export async function generateMetadata(): Promise<Metadata> {
  const profile = await getProfile();
  return {
    title: profile.name ? `Tentang ${profile.name}` : 'Tentang Saya',
    description:
      profile.bio_text || profile.headline || 'Profil, keahlian, dan tautan media sosial.',
    alternates: { canonical: '/about' },
    openGraph: {
      type: 'profile',
      title: profile.name || 'Tentang Saya',
      description: profile.bio_text || undefined,
      images: profile.avatar ? [profile.avatar] : [],
    },
  };
}
export default function AboutPage() {
  return (
    <Suspense fallback={<RouteLoading variant="profile" />}>
      <AboutPageContent />
    </Suspense>
  );
}

async function AboutPageContent() {
  const profile = await getProfile();
  const socials = [
    ['GitHub', safeExternalUrl(profile.github)],
    ['LinkedIn', safeExternalUrl(profile.linkedin)],
    ['Instagram', safeExternalUrl(profile.instagram)],
    ['Twitter / X', safeExternalUrl(profile.twitter)],
    ['Website', safeExternalUrl(profile.website)],
  ].filter((item): item is [string, string] => Boolean(item[1]));
  return (
    <div className="mx-auto max-w-3xl px-4 py-8 sm:px-6 sm:py-16">
      <div className="overflow-hidden rounded-2xl border border-stone-200/80 bg-white p-6 shadow-sm sm:p-10">
        <header className="flex flex-col items-start gap-6 border-b border-stone-100 pb-8 sm:flex-row sm:items-center">
          {profile.avatar ? (
            <AvatarLightbox src={profile.avatar} alt={profile.name || 'Foto profil'} large />
          ) : (
            <div className="flex h-28 w-28 shrink-0 items-center justify-center rounded-full bg-stone-900 text-4xl font-bold text-white shadow-sm ring-4 ring-stone-100">
              {(profile.name || 'P').charAt(0)}
            </div>
          )}
          <div className="space-y-2">
            <div className="flex items-center justify-center gap-2 sm:justify-start">
              <span className="inline-block h-2.5 w-2.5 animate-pulse rounded-full bg-emerald-500" />
              <span className="text-xs font-semibold tracking-wider text-stone-400 uppercase">
                Software Engineer
              </span>
            </div>
            <h1 className="text-2xl font-extrabold tracking-tight text-stone-900 sm:text-3xl">
              {profile.name || 'Profile'}
            </h1>
            {profile.headline && (
              <p className="text-sm font-semibold text-stone-700">{profile.headline}</p>
            )}
            {profile.location && (
              <p className="text-xs font-medium text-stone-500">⌖ {profile.location}</p>
            )}
          </div>
        </header>
        {profile.bio && (
          <section className="py-8">
            <h2 className="mb-4 text-xs font-bold tracking-wider text-stone-400 uppercase">
              Tentang Saya
            </h2>
            <div
              className="article-content font-serif text-base leading-relaxed text-stone-700"
              dangerouslySetInnerHTML={{ __html: sanitizeHtml(profile.bio) }}
            />
          </section>
        )}
        <section className="border-t border-stone-100 py-6">
          <h2 className="mb-4 text-xs font-bold tracking-wider text-stone-400 uppercase">
            Fokus Utama
          </h2>
          <div className="grid grid-cols-1 gap-3 sm:grid-cols-2">
            <div className="rounded-xl border border-stone-200/60 bg-stone-50/50 p-4">
              <p className="text-xs font-bold text-stone-900">Backend & API Architecture</p>
              <p className="mt-1 text-xs text-stone-500">
                Membangun arsitektur server yang scalable, clean code, dan RESTful API.
              </p>
            </div>
            <div className="rounded-xl border border-stone-200/60 bg-stone-50/50 p-4">
              <p className="text-xs font-bold text-stone-900">Modern Web Development</p>
              <p className="mt-1 text-xs text-stone-500">
                Eksplorasi ekosistem Laravel, Blade, Tailwind CSS, dan integrasi UI responsif.
              </p>
            </div>
          </div>
        </section>
        {profile.skills.length > 0 && (
          <section className="border-t border-stone-100 py-6">
            <h2 className="mb-3 text-xs font-bold tracking-wider text-stone-400 uppercase">
              Keahlian & Tech Stack
            </h2>
            <div className="flex flex-wrap gap-2">
              {profile.skills.map((skill) => (
                <span
                  key={skill.id}
                  className="rounded-lg border border-stone-200/80 bg-stone-100/70 px-3 py-1 text-xs font-semibold text-stone-700"
                >
                  {skill.name}
                </span>
              ))}
            </div>
          </section>
        )}
        {socials.length > 0 && (
          <section className="border-t border-stone-100 py-6">
            <h2 className="mb-3 text-xs font-bold tracking-wider text-stone-400 uppercase">
              Tautan & Media Sosial
            </h2>
            <div className="flex flex-wrap gap-2.5">
              {socials.map(([label, url]) => (
                <a
                  key={label}
                  href={url}
                  target="_blank"
                  rel="noopener noreferrer"
                  className="inline-flex items-center gap-2 rounded-xl border border-stone-200/90 bg-stone-50/50 px-3.5 py-2 text-xs font-semibold text-stone-700 shadow-sm transition-all hover:border-stone-400 hover:bg-stone-100 hover:text-stone-900"
                >
                  {label} ↗
                </a>
              ))}
            </div>
          </section>
        )}
        <div className="mt-4 rounded-xl border border-stone-200/80 bg-stone-900 p-6 text-center text-white sm:text-left">
          <p className="text-sm font-bold">Tertarik berkolaborasi?</p>
          <p className="mt-0.5 text-xs text-stone-400">
            Terbuka untuk diskusi proyek, konsultasi teknis, atau sekadar bertegur sapa.
          </p>
        </div>

        <CollaborationForm />
      </div>
    </div>
  );
}
