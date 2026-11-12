'use client';

import { useEffect, useState } from 'react';
import { SmartImage } from '@/components/ui/smart-image';

interface AvatarLightboxProps {
  src: string;
  alt: string;
  large?: boolean;
}

function ZoomIcon() {
  return (
    <svg
      xmlns="http://www.w3.org/2000/svg"
      viewBox="0 0 24 24"
      width="20"
      height="20"
      fill="none"
      stroke="currentColor"
      strokeWidth="1.8"
      strokeLinecap="round"
      strokeLinejoin="round"
      aria-hidden="true"
      className="h-5 w-5"
    >
      <circle cx="11" cy="11" r="7" />
      <path d="m16 16 4 4" />
      <path d="M11 8v6M8 11h6" />
    </svg>
  );
}

function CloseIcon() {
  return (
    <svg
      xmlns="http://www.w3.org/2000/svg"
      viewBox="0 0 24 24"
      width="20"
      height="20"
      fill="none"
      stroke="currentColor"
      strokeWidth="2"
      strokeLinecap="round"
      strokeLinejoin="round"
      aria-hidden="true"
      className="h-5 w-5"
    >
      <path d="m18 6-12 12M6 6l12 12" />
    </svg>
  );
}

export function AvatarLightbox({ src, alt, large = false }: AvatarLightboxProps) {
  const [open, setOpen] = useState(false);

  useEffect(() => {
    if (!open) return;

    const previousOverflow = document.body.style.overflow;

    const handleKeyDown = (event: KeyboardEvent) => {
      if (event.key === 'Escape') {
        setOpen(false);
      }
    };

    document.body.style.overflow = 'hidden';
    window.addEventListener('keydown', handleKeyDown);

    return () => {
      document.body.style.overflow = previousOverflow;
      window.removeEventListener('keydown', handleKeyDown);
    };
  }, [open]);

  return (
    <>
      {/* =====================================================
          AVATAR TRIGGER
      ===================================================== */}
      <button
        type="button"
        onClick={() => setOpen(true)}
        aria-label={`Perbesar foto: ${alt}`}
        aria-haspopup="dialog"
        aria-expanded={open}
        className={`group relative block shrink-0 overflow-hidden rounded-full border border-stone-200 bg-white p-1 shadow-sm transition-all duration-300 hover:border-stone-400 hover:shadow-md focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-stone-500 ${large ? 'h-32 w-32' : 'h-24 w-24'} `}
      >
        <span className="relative block h-full w-full overflow-hidden rounded-full">
          <SmartImage
            src={src}
            alt={alt}
            width={large ? 120 : 88}
            height={large ? 120 : 88}
            className="h-full w-full rounded-full object-cover transition-transform duration-500 ease-out group-hover:scale-110"
          />

          {/* Hover Overlay */}
          <span
            className="absolute inset-0 flex items-center justify-center rounded-full bg-stone-950/35 text-white opacity-0 transition-opacity duration-300 group-hover:opacity-100 group-focus-visible:opacity-100"
            aria-hidden="true"
          >
            <ZoomIcon />
          </span>
        </span>
      </button>

      {/* =====================================================
          LIGHTBOX MODAL
      ===================================================== */}
      {open && (
        <div
          role="dialog"
          aria-modal="true"
          aria-label="Foto profil"
          onClick={() => setOpen(false)}
          className="animate-in fade-in fixed inset-0 z-[100] flex items-center justify-center bg-stone-950/85 p-4 backdrop-blur-md duration-200"
        >
          {/* Background */}
          <div className="absolute inset-0" aria-hidden="true" />

          {/* Modal Content */}
          <div
            onClick={(event) => event.stopPropagation()}
            className="animate-in zoom-in-95 relative z-10 w-full max-w-2xl duration-200"
          >
            {/* Close Button */}
            <button
              type="button"
              onClick={() => setOpen(false)}
              aria-label="Tutup foto"
              autoFocus
              className="absolute -top-12 right-0 z-20 flex h-10 w-10 items-center justify-center rounded-full border border-white/20 bg-white/10 text-white backdrop-blur-md transition-all duration-200 hover:rotate-90 hover:bg-white/20 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white"
            >
              <CloseIcon />
            </button>

            {/* Image Container */}
            <div className="overflow-hidden rounded-2xl border border-white/10 bg-white/5 p-2 shadow-2xl sm:p-3">
              <SmartImage
                src={src}
                alt={alt}
                width={1200}
                height={1200}
                className="mx-auto max-h-[75vh] w-full rounded-xl object-contain"
              />
            </div>

            {/* Caption */}
            <p className="mt-4 text-center text-xs font-medium tracking-wide text-white/60">
              {alt}
            </p>
          </div>
        </div>
      )}
    </>
  );
}
