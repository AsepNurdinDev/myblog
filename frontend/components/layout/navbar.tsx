'use client';

import Link from 'next/link';
import Image from 'next/image';
import { usePathname } from 'next/navigation';
import { useState, useEffect } from 'react';
import { safeExternalUrl } from '@/lib/utils/url';

const links = [
  { href: '/articles', label: 'Catatan' },
  { href: '/projects', label: 'Project' },
  { href: '/about', label: 'Tentang' },
];

export function Navbar({ github }: { github?: string | null }) {
  const [open, setOpen] = useState(false);
  const [scrolled, setScrolled] = useState(false);
  const [logoError, setLogoError] = useState(false);

  const pathname = usePathname();

  const githubUrl = safeExternalUrl(github) || 'https://github.com/asepnrdn';

  // Handle navbar scroll effect
  useEffect(() => {
    const handleScroll = () => {
      setScrolled(window.scrollY > 20);
    };

    handleScroll();

    window.addEventListener('scroll', handleScroll, {
      passive: true,
    });

    return () => {
      window.removeEventListener('scroll', handleScroll);
    };
  }, []);

  return (
    <header
      className={`sticky top-0 z-40 transition-all duration-300 ${
        scrolled
          ? 'border-b border-stone-200/80 bg-white/90 shadow-2xs backdrop-blur-md'
          : 'border-b border-stone-200/50 bg-stone-50/80 backdrop-blur-md'
      }`}
    >
      <nav
        className="mx-auto flex h-16 max-w-5xl items-center justify-between px-4 sm:px-6"
        aria-label="Navigasi utama"
      >
        {/* Logo / Brand */}
        <Link
          href="/"
          className="group flex items-center gap-2.5 text-lg font-bold tracking-tight text-stone-900 transition"
          aria-label="AsepBlog Homepage"
        >
          {!logoError ? (
            <div className="relative h-8 w-8 overflow-hidden rounded-lg border border-stone-200 shadow-2xs transition group-hover:border-stone-400">
              <Image
                src="/asepblog.png"
                alt="AsepBlog Logo"
                fill
                sizes="32px"
                className="object-cover"
                onError={() => setLogoError(true)}
                priority
              />
            </div>
          ) : (
            <span className="flex h-8 w-8 items-center justify-center rounded-lg bg-stone-900 text-xs text-white shadow-xs transition group-hover:bg-stone-700">
              A
            </span>
          )}

          <span className="font-sans font-extrabold tracking-tight">AsepBlog</span>
        </Link>

        {/* Desktop Navigation */}
        <div className="hidden items-center gap-6 sm:flex">
          {/* Search */}
          <form action="/articles" method="GET" className="relative">
            <input
              name="search"
              placeholder="Cari artikel..."
              aria-label="Cari artikel"
              className="w-48 rounded-full border border-stone-200 bg-stone-100/60 px-3.5 py-1.5 pl-9 text-xs text-stone-800 placeholder-stone-400 transition focus:w-60 focus:border-stone-400 focus:bg-white focus:ring-2 focus:ring-stone-900/10 focus:outline-hidden"
            />

            <svg
              className="pointer-events-none absolute top-1/2 left-3 h-3.5 w-3.5 -translate-y-1/2 text-stone-400"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              strokeWidth="2"
              aria-hidden="true"
            >
              <path d="m21 21-6-6m2-5a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z" />
            </svg>
          </form>

          {/* Navigation Links */}
          <div className="flex items-center gap-6 border-l border-stone-200/80 pl-6">
            {links.map((link) => {
              const isActive = pathname === link.href || pathname.startsWith(`${link.href}/`);

              return (
                <Link
                  key={link.href}
                  href={link.href}
                  aria-current={isActive ? 'page' : undefined}
                  className={`relative py-1 text-xs font-medium tracking-wide transition sm:text-sm ${
                    isActive
                      ? 'font-semibold text-stone-900'
                      : 'text-stone-500 hover:text-stone-900'
                  }`}
                >
                  {link.label}

                  {isActive && (
                    <span className="absolute inset-x-0 -bottom-1 h-0.5 rounded-full bg-stone-900" />
                  )}
                </Link>
              );
            })}

            {/* GitHub Button */}
            <a
              href={githubUrl}
              target="_blank"
              rel="noopener noreferrer"
              aria-label="GitHub Profile"
              className="group flex shrink-0 items-center gap-2 rounded-full border border-stone-300/80 bg-stone-900 px-3.5 py-1.5 text-xs font-semibold text-white shadow-xs transition hover:border-stone-700 hover:bg-stone-800 hover:shadow-md"
            >
              <GithubIcon className="h-[18px] w-[18px] shrink-0" />

              <span>GitHub</span>
            </a>
          </div>
        </div>

        {/* Mobile Menu Button */}
        <button
          type="button"
          onClick={() => setOpen((prev) => !prev)}
          aria-expanded={open}
          aria-label={open ? 'Tutup menu' : 'Buka menu'}
          aria-controls="mobile-navigation"
          className="inline-flex items-center justify-center rounded-xl p-2.5 text-stone-700 transition hover:bg-stone-200/60 hover:text-stone-900 sm:hidden"
        >
          {open ? <CloseIcon /> : <MenuIcon />}
        </button>
      </nav>

      {/* Mobile Backdrop */}
      {open && (
        <div
          className="fixed inset-x-0 top-16 bottom-0 z-40 bg-stone-900/20 backdrop-blur-xs sm:hidden"
          onClick={() => setOpen(false)}
          aria-hidden="true"
        />
      )}

      {/* Mobile Dropdown Menu */}
      {open && (
        <div
          id="mobile-navigation"
          className="animate-in fade-in slide-in-from-top-2 absolute inset-x-0 top-16 z-50 border-b border-stone-200 bg-white/95 p-5 shadow-xl backdrop-blur-xl sm:hidden"
        >
          <div className="space-y-4">
            {/* Mobile Search */}
            <form
              action="/articles"
              method="GET"
              onSubmit={() => setOpen(false)}
              className="relative"
            >
              <input
                name="search"
                placeholder="Cari artikel, topik, atau kata kunci..."
                aria-label="Cari artikel"
                className="w-full rounded-xl border border-stone-200 bg-stone-50 px-4 py-2.5 pl-10 text-sm text-stone-900 placeholder-stone-400 focus:border-stone-400 focus:bg-white focus:ring-2 focus:ring-stone-900/10 focus:outline-hidden"
              />

              <svg
                className="pointer-events-none absolute top-1/2 left-3.5 h-4 w-4 -translate-y-1/2 text-stone-400"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                strokeWidth="2"
                aria-hidden="true"
              >
                <path d="m21 21-6-6m2-5a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z" />
              </svg>
            </form>

            {/* Mobile Navigation Links */}
            <div className="space-y-1 border-t border-stone-100 pt-3">
              {links.map((link) => {
                const isActive = pathname === link.href || pathname.startsWith(`${link.href}/`);

                return (
                  <Link
                    key={link.href}
                    href={link.href}
                    onClick={() => setOpen(false)}
                    aria-current={isActive ? 'page' : undefined}
                    className={`flex items-center justify-between rounded-xl px-4 py-3 text-sm font-medium transition ${
                      isActive
                        ? 'bg-stone-900 font-semibold text-white shadow-sm'
                        : 'text-stone-600 hover:bg-stone-100 hover:text-stone-900'
                    }`}
                  >
                    <span>{link.label}</span>
                    <span className="text-xs opacity-60">→</span>
                  </Link>
                );
              })}

              {/* Mobile GitHub Button */}
              <a
                href={githubUrl}
                target="_blank"
                rel="noopener noreferrer"
                onClick={() => setOpen(false)}
                className="flex items-center gap-3 rounded-xl border border-stone-800 bg-stone-900 px-4 py-3 text-sm font-medium text-white transition hover:bg-stone-800"
              >
                <GithubIcon className="h-5 w-5 shrink-0" />

                <span className="font-semibold">GitHub Profile</span>

                <span className="ml-auto text-xs opacity-60">↗</span>
              </a>
            </div>
          </div>
        </div>
      )}
    </header>
  );
}

/* Menu Icon */
function MenuIcon() {
  return (
    <svg
      className="h-6 w-6"
      viewBox="0 0 24 24"
      fill="none"
      stroke="currentColor"
      strokeWidth="2.2"
      aria-hidden="true"
    >
      <path strokeLinecap="round" strokeLinejoin="round" d="M4 6h16M4 12h16M4 18h16" />
    </svg>
  );
}

/* Close Icon */
function CloseIcon() {
  return (
    <svg
      className="h-6 w-6"
      viewBox="0 0 24 24"
      fill="none"
      stroke="currentColor"
      strokeWidth="2.2"
      aria-hidden="true"
    >
      <path strokeLinecap="round" strokeLinejoin="round" d="m6 6 12 12M18 6 6 18" />
    </svg>
  );
}

/* GitHub Icon - Clean Outline */
function GithubIcon({ className = 'h-5 w-5' }: { className?: string }) {
  return (
    <svg
      className={className}
      viewBox="0 0 24 24"
      fill="none"
      stroke="currentColor"
      strokeWidth="1.8"
      strokeLinecap="round"
      strokeLinejoin="round"
      aria-hidden="true"
    >
      <path d="M9 19c-5 1.5-5-2.5-7-3m14 6v-3.87a3.37 3.37 0 0 0-.94-2.61c3.14-.35 6.44-1.54 6.44-7A5.44 5.44 0 0 0 20 4.77 5.07 5.07 0 0 0 19.91 1S18.73.65 16 2.48a13.38 13.38 0 0 0-7 0C6.27.65 5.09 1 5.09 1A5.07 5.07 0 0 0 5 4.77a5.44 5.44 0 0 0-1.5 3.78c0 5.42 3.3 6.61 6.44 7A3.37 3.37 0 0 0 9 18.13V22" />
    </svg>
  );
}
