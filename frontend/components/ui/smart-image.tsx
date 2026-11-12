'use client';

import Image from 'next/image';
import { useState } from 'react';

type Props = {
  src: string;
  alt: string;
  className?: string;
  width?: number;
  height?: number;
  fill?: boolean;
  sizes?: string;
  loading?: 'lazy' | 'eager';
};

const FALLBACK_IMAGE = '/asepblog.png';

export function SmartImage({
  src,
  alt,
  className,
  width,
  height,
  fill = false,
  sizes,
  loading = 'lazy',
}: Props) {
  const [failed, setFailed] = useState(false);
  const isSafeSource = /^(https?:\/\/|\/(?!\/))/.test(src);
  const source = failed || !isSafeSource ? FALLBACK_IMAGE : src;
  const isRemoteSource = /^https?:\/\//i.test(source);

  return (
    <Image
      src={source}
      alt={alt}
      className={className}
      {...(fill ? { fill: true } : { width: width || 1200, height: height || 800 })}
      sizes={sizes || (fill ? '(max-width: 640px) 100vw, 50vw' : undefined)}
      loading={loading}
      // Laravel media is served directly by the browser. This avoids requiring
      // the Next.js server/container to reach the API host for image optimization.
      unoptimized={isRemoteSource}
      onError={() => setFailed(true)}
    />
  );
}
