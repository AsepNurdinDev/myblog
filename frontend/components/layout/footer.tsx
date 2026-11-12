'use client';

import Link from 'next/link';
import Image from 'next/image';
import { useState } from 'react';
import type { Profile } from '@/types/api';
import { safeExternalUrl } from '@/lib/utils/url';

const navigationLinks = [
  { label: 'Beranda', href: '/' },
  { label: 'Catatan', href: '/articles' },
  { label: 'Project', href: '/projects' },
  { label: 'Tentang', href: '/about' },
];

const GitHubIcon = ({ className = 'h-5 w-5' }: { className?: string }) => (
  <svg
    xmlns="http://www.w3.org/2000/svg"
    viewBox="0 0 24 24"
    width="24"
    height="24"
    fill="currentColor"
    className={`block shrink-0 ${className}`}
    aria-hidden="true"
    focusable="false"
  >
    <path
      fillRule="evenodd"
      clipRule="evenodd"
      d="M12 .75a11.25 11.25 0 0 0-3.56 21.92c.56.1.77-.24.77-.54v-2.1c-3.14.68-3.8-1.33-3.8-1.33-.51-1.3-1.25-1.65-1.25-1.65-1.02-.7.08-.68.08-.68 1.13.08 1.72 1.16 1.72 1.16 1 1.72 2.63 1.22 3.27.93.1-.73.4-1.22.72-1.5-2.5-.28-5.13-1.25-5.13-5.56 0-1.23.44-2.23 1.16-3.01-.12-.29-.5-1.43.11-2.98 0 0 .95-.3 3.1 1.15a10.77 10.77 0 0 1 5.64 0c2.15-1.45 3.1-1.15 3.1-1.15.62 1.55.23 2.69.12 2.98.72.78 1.15 1.78 1.15 3.01 0 4.32-2.63 5.27-5.14 5.55.41.36.77 1.04.77 2.1v3.12c0 .3.2.65.78.54A11.25 11.25 0 0 0 12 .75Z"
    />
  </svg>
);

const ExternalIcon = () => (
  <svg
    xmlns="http://www.w3.org/2000/svg"
    viewBox="0 0 24 24"
    width="14"
    height="14"
    fill="none"
    stroke="currentColor"
    strokeWidth="1.8"
    strokeLinecap="round"
    strokeLinejoin="round"
    className="h-3.5 w-3.5 shrink-0"
    aria-hidden="true"
  >
    <path d="M7 17 17 7M7 7h10v10" />
  </svg>
);

export function Footer({ profile }: { profile: Profile | null }) {
  const [logoError, setLogoError] = useState(false);

  const socials = [
    {
      label: 'GitHub',
      url: safeExternalUrl(profile?.github) || 'https://github.com/asepnrdn',
      icon: 'github',
    },
    {
      label: 'LinkedIn',
      url: safeExternalUrl(profile?.linkedin) || 'https://linkedin.com/in/asepnrdn',
    },
    {
      label: 'Instagram',
      url: safeExternalUrl(profile?.instagram),
    },
    {
      label: 'Twitter',
      url: safeExternalUrl(profile?.twitter),
    },
    {
      label: 'Website',
      url: safeExternalUrl(profile?.website),
    },
  ].filter((item) => Boolean(item.url));

  return (
    <footer className="mt-20 border-t border-stone-200 bg-stone-100/60">
      <div className="mx-auto max-w-6xl px-5 py-12 sm:px-6 sm:py-16 lg:px-8">
        {/* =====================================================
            MAIN FOOTER
        ===================================================== */}
        <div className="grid grid-cols-1 gap-12 sm:grid-cols-2 lg:grid-cols-12 lg:gap-10">
          {/* BRAND */}
          <div className="space-y-5 lg:col-span-5">
            <Link
              href="/"
              aria-label="AsepBlog - Beranda"
              className="group inline-flex items-center gap-3"
            >
              {!logoError ? (
                <div className="relative h-10 w-10 shrink-0 overflow-hidden rounded-xl border border-stone-200 bg-white shadow-sm transition duration-300 group-hover:border-stone-400">
                  <Image
                    src="/asepblog.png"
                    alt="AsepBlog Logo"
                    fill
                    sizes="40px"
                    className="object-cover"
                    onError={() => setLogoError(true)}
                  />
                </div>
              ) : (
                <span className="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-stone-900 text-sm font-bold text-white shadow-sm">
                  A
                </span>
              )}

              <span className="text-xl font-extrabold tracking-tight text-stone-950">AsepBlog</span>
            </Link>

            <div className="max-w-sm space-y-3">
              <p className="text-sm leading-7 text-stone-600">
                Sebuah ruang personal untuk berbagi cerita, pengalaman, pemikiran, perjalanan, dan
                berbagai hal yang menarik untuk dipelajari.
              </p>

              <p className="text-sm leading-7 text-stone-500">
                Dari kehidupan sehari-hari hingga teknologi, pengembangan perangkat lunak, dan
                eksplorasi berbagai pengetahuan.
              </p>
            </div>

            {/* Personal Blog Label */}
            <div className="inline-flex items-center gap-2 rounded-full border border-stone-200 bg-white px-3 py-2">
              <span className="h-1.5 w-1.5 rounded-full bg-stone-900" />
              <span className="text-[11px] font-semibold tracking-wide text-stone-600">
                PERSONAL BLOG & JOURNAL
              </span>
            </div>
          </div>

          {/* NAVIGATION */}
          <div className="lg:col-span-3">
            <h3 className="mb-5 text-xs font-bold tracking-[0.15em] text-stone-900 uppercase">
              Navigasi
            </h3>

            <ul className="space-y-3.5">
              {navigationLinks.map((link) => (
                <li key={link.href}>
                  <Link
                    href={link.href}
                    className="group inline-flex items-center gap-2 text-sm text-stone-500 transition-colors duration-200 hover:text-stone-950"
                  >
                    <span>{link.label}</span>

                    <span
                      aria-hidden="true"
                      className="translate-x-0 text-stone-300 opacity-0 transition-all duration-200 group-hover:translate-x-1 group-hover:opacity-100"
                    >
                      →
                    </span>
                  </Link>
                </li>
              ))}
            </ul>
          </div>

          {/* SOCIAL LINKS */}
          <div className="lg:col-span-4">
            <h3 className="mb-5 text-xs font-bold tracking-[0.15em] text-stone-900 uppercase">
              Terhubung
            </h3>

            <p className="mb-5 max-w-xs text-sm leading-6 text-stone-500">
              Temukan saya di berbagai platform dan mari terhubung untuk berbagi ide maupun
              pengalaman.
            </p>

            <div className="flex flex-wrap gap-2.5">
              {socials.map((social) => (
                <a
                  key={social.label}
                  href={social.url!}
                  target="_blank"
                  rel="noopener noreferrer"
                  aria-label={`${social.label} (buka tab baru)`}
                  className={`group inline-flex min-h-10 items-center justify-center gap-2 rounded-full border px-3.5 py-2 text-xs font-semibold transition-all duration-200 ${
                    social.icon === 'github'
                      ? 'border-stone-900 bg-stone-900 text-white shadow-sm hover:border-stone-700 hover:bg-stone-700 hover:shadow-md'
                      : 'border-stone-200 bg-white text-stone-600 hover:border-stone-400 hover:bg-stone-50 hover:text-stone-950'
                  }`}
                >
                  {social.icon === 'github' && <GitHubIcon className="h-5 w-5" />}

                  <span>{social.label}</span>

                  {social.icon !== 'github' && <ExternalIcon />}
                </a>
              ))}
            </div>
          </div>
        </div>

        {/* =====================================================
            BOTTOM FOOTER
        ===================================================== */}
        <div className="mt-12 border-t border-stone-200 pt-7 sm:mt-16 sm:pt-8">
          <div className="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div className="space-y-1 text-center sm:text-left">
              <p className="text-xs font-medium text-stone-600">
                © {new Date().getFullYear()} AsepBlog. All rights reserved.
              </p>

              <p className="text-[11px] text-stone-400">
                Personal space for stories, ideas, and knowledge.
              </p>
            </div>

            <Link
              href="/"
              className="group inline-flex items-center justify-center gap-2 text-xs font-medium text-stone-400 transition hover:text-stone-900"
            >
              Back to top
              <span
                aria-hidden="true"
                className="transition-transform duration-200 group-hover:-translate-y-1"
              >
                ↑
              </span>
            </Link>
          </div>
        </div>
      </div>
    </footer>
  );
}
