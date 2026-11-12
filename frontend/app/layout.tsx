import type { Metadata, Viewport } from 'next';
import type { ReactNode } from 'react';
import './globals.css';
export const metadata: Metadata = {
  metadataBase: new URL('https://asepblog.online'),
  title: { default: 'AsepBlog — Catatan, Journal & Project Portfolio', template: '%s — AsepBlog' },
  description: 'Ruang berbagi catatan teknis dan portfolio proyek.',
  applicationName: 'AsepBlog',
  openGraph: {
    type: 'website',
    siteName: 'AsepBlog',
    title: 'AsepBlog',
    description: 'Catatan, journal, dan project portfolio.',
  },
  twitter: {
    card: 'summary_large_image',
    title: 'AsepBlog',
    description: 'Catatan, journal, dan project portfolio.',
  },
};
export const viewport: Viewport = { themeColor: '#fafaf9' };
export default function RootLayout({ children }: Readonly<{ children: ReactNode }>) {
  return (
    <html lang="id">
      <body>{children}</body>
    </html>
  );
}
