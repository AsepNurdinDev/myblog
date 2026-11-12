import type { ReactNode } from 'react';
import { Suspense, cache } from 'react';
import { Navbar } from './navbar';
import { Footer } from './footer';
import { getProfile } from '@/lib/api/resources';
import type { Profile } from '@/types/api';

const publicProfile = cache(async (): Promise<Profile | null> => {
  try {
    return await getProfile();
  } catch {
    return null;
  }
});
async function HeaderWithProfile() {
  const profile = await publicProfile();
  return <Navbar github={profile?.github} />;
}
async function FooterWithProfile() {
  const profile = await publicProfile();
  return <Footer profile={profile} />;
}
function PageFallback() {
  return (
    <div className="mx-auto max-w-5xl px-4 py-16 sm:px-6">
      <div className="animate-pulse space-y-5">
        <div className="h-8 w-1/3 rounded bg-stone-200" />
        <div className="h-4 w-2/3 rounded bg-stone-200" />
        <div className="grid grid-cols-1 gap-6 sm:grid-cols-3">
          {[1, 2, 3].map((item) => (
            <div key={item} className="h-64 rounded-2xl border border-stone-200 bg-white" />
          ))}
        </div>
      </div>
    </div>
  );
}
export function PublicLayout({ children }: { children: ReactNode }) {
  return (
    <div className="flex min-h-screen flex-col">
      <Suspense fallback={<Navbar />}>
        <HeaderWithProfile />
      </Suspense>
      <main className="flex-1">
        <Suspense fallback={<PageFallback />}>{children}</Suspense>
      </main>
      <Suspense fallback={<Footer profile={null} />}>
        <FooterWithProfile />
      </Suspense>
    </div>
  );
}
