import type { ReactNode } from 'react';
import { PublicLayout } from '@/components/layout/public-layout';
export default function SiteLayout({ children }: { children: ReactNode }) {
  return <PublicLayout>{children}</PublicLayout>;
}
