import Link from 'next/link';
import { Suspense } from 'react';
import { getHome } from '@/lib/api/resources';
import { PostCard } from '@/components/articles/post-card';
import { ProjectCard } from '@/components/projects/project-card';
import { SearchForm } from '@/components/ui/search-form';
import { EmptyState } from '@/components/ui/empty-state';
import { AvatarLightbox } from '@/components/home/avatar-lightbox';

export const metadata = {
  title: 'Catatan, Journal & Project Portfolio',
  description:
    'AsepBlog adalah ruang personal untuk berbagi cerita, pengalaman, pemikiran, perjalanan, teknologi, dan catatan kehidupan.',
};

function HomeLoading() {
  return (
    <main className="mx-auto max-w-6xl px-5 py-10 sm:px-6 lg:px-8">
      <div className="animate-pulse space-y-14">
        <section className="flex flex-col gap-6 border-b border-stone-200 pb-12 sm:flex-row sm:items-center sm:gap-8">
          <div className="h-28 w-28 shrink-0 rounded-full bg-stone-200" />

          <div className="flex-1 space-y-4">
            <div className="h-3 w-36 rounded bg-stone-200" />
            <div className="h-9 w-64 max-w-full rounded bg-stone-200" />
            <div className="h-4 w-full max-w-xl rounded bg-stone-200" />
            <div className="h-4 w-3/4 max-w-lg rounded bg-stone-200" />
          </div>
        </section>

        <section className="space-y-4">
          <div className="h-11 w-full max-w-2xl rounded-xl bg-stone-200" />
        </section>

        <section className="space-y-5">
          <div className="h-7 w-48 rounded bg-stone-200" />
          <div className="flex gap-3">
            {[1, 2, 3, 4, 5].map((item) => (
              <div key={item} className="h-9 w-24 rounded-full bg-stone-200" />
            ))}
          </div>
        </section>

        <section className="space-y-6">
          <div className="h-7 w-48 rounded bg-stone-200" />
          <div className="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
            {[1, 2, 3, 4, 5, 6].map((item) => (
              <div key={item} className="h-64 rounded-2xl border border-stone-200 bg-stone-100" />
            ))}
          </div>
        </section>
      </div>
    </main>
  );
}

export default function HomePage() {
  return (
    <Suspense fallback={<HomeLoading />}>
      <HomeContent />
    </Suspense>
  );
}

async function HomeContent() {
  const home = await getHome();
  const profile = home.profile;

  return (
    <main className="mx-auto max-w-6xl px-5 py-10 font-sans sm:px-6 sm:py-14 lg:px-8">
      {/* =====================================================
          HERO / PROFILE
      ===================================================== */}
      <section className="border-b border-stone-200 pb-12 sm:pb-16">
        <div className="flex flex-col gap-7 sm:flex-row sm:items-center sm:gap-8 lg:gap-10">
          {/* Profile Avatar */}
          <div className="shrink-0">
            {profile?.avatar ? (
              <AvatarLightbox src={profile.avatar} alt={profile.name || 'Foto profil'} />
            ) : (
              <div className="flex h-24 w-24 items-center justify-center rounded-full bg-stone-900 text-3xl font-bold text-white shadow-sm sm:h-28 sm:w-28">
                {(profile?.name || 'A').charAt(0)}
              </div>
            )}
          </div>

          {/* Profile Information */}
          <div className="max-w-3xl space-y-4">
            <div className="flex items-center gap-2">
              <span className="h-1.5 w-1.5 rounded-full bg-stone-900" />
              <p className="text-xs font-semibold tracking-[0.16em] text-stone-500 uppercase">
                Personal Blog & Journal
              </p>
            </div>

            <div className="space-y-2">
              <h1 className="text-3xl font-extrabold tracking-tight text-stone-950 sm:text-4xl lg:text-5xl">
                {profile?.name || 'Asep Nurdin'}
              </h1>

              {profile?.headline && (
                <p className="text-sm font-semibold text-stone-700 sm:text-base">
                  {profile.headline}
                </p>
              )}
            </div>

            <p className="max-w-2xl font-serif text-base leading-8 text-stone-600 sm:text-lg sm:leading-8">
              {profile?.bio_text ||
                'Selamat datang di AsepBlog. Sebuah ruang untuk berbagi cerita, mencatat perjalanan, menuangkan pemikiran, dan mendokumentasikan berbagai hal yang saya pelajari. Mulai dari kehidupan sehari-hari hingga teknologi dan pengembangan perangkat lunak.'}
            </p>

            <div className="flex flex-wrap items-center gap-3 pt-1">
              <Link
                href="/about"
                className="inline-flex items-center gap-2 text-sm font-semibold text-stone-900 transition hover:text-stone-500"
              >
                Mengenal saya
                <span aria-hidden="true">→</span>
              </Link>
            </div>
          </div>
        </div>
      </section>

      {/* =====================================================
          SEARCH
      ===================================================== */}
      <section className="mt-8 sm:mt-10">
        <div className="max-w-2xl">
          <SearchForm />
        </div>
      </section>

      {/* =====================================================
          CATEGORIES
      ===================================================== */}
      {home.categories.length > 0 && (
        <section className="mt-12 sm:mt-14">
          <div className="mb-4">
            <p className="mb-1 text-xs font-semibold tracking-widest text-stone-400 uppercase">
              Explore
            </p>

            <h2 className="text-xl font-extrabold tracking-tight text-stone-950 sm:text-2xl">
              Topik & Kategori
            </h2>

            <p className="mt-2 max-w-xl text-sm leading-6 text-stone-500">
              Beragam cerita, pengalaman, pemikiran, dan pengetahuan dari berbagai hal yang menarik
              untuk dibagikan.
            </p>
          </div>

          <div className="flex flex-wrap gap-2">
            {home.categories.map((category) => (
              <Link
                key={category.id}
                href={`/categories/${category.slug}`}
                className="inline-flex items-center rounded-full border border-stone-200 bg-white px-4 py-2 text-xs font-medium text-stone-700 transition duration-200 hover:border-stone-900 hover:bg-stone-900 hover:text-white sm:text-sm"
              >
                {category.name}
              </Link>
            ))}
          </div>
        </section>
      )}

      {/* =====================================================
          LATEST ARTICLES
      ===================================================== */}
      <section className="mt-14 sm:mt-20">
        {/* Section Heading */}
        <div className="mb-7 flex items-end justify-between gap-4 sm:mb-8">
          <div>
            <p className="mb-1 text-xs font-semibold tracking-widest text-stone-400 uppercase">
              Recently Published
            </p>

            <h2 className="text-2xl font-extrabold tracking-tight text-stone-950 sm:text-3xl">
              Catatan Terbaru
            </h2>

            <p className="mt-2 text-sm text-stone-500">
              Cerita, jurnal, dan berbagai hal yang ingin dibagikan.
            </p>
          </div>

          <Link
            href="/articles"
            className="hidden shrink-0 items-center gap-2 text-sm font-semibold text-stone-800 transition hover:text-stone-500 sm:inline-flex"
          >
            Lihat semua
            <span aria-hidden="true">→</span>
          </Link>
        </div>

        {/* Article Cards */}
        {home.latest_posts.length > 0 ? (
          <>
            <div className="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3 lg:gap-6">
              {home.latest_posts.map((post) => (
                <PostCard key={post.id} post={post} />
              ))}
            </div>

            <div className="mt-8 sm:hidden">
              <Link
                href="/articles"
                className="flex w-full items-center justify-center gap-2 rounded-xl border border-stone-200 bg-white px-5 py-3 text-sm font-semibold text-stone-800 transition hover:border-stone-400 hover:bg-stone-50"
              >
                Lihat Semua Catatan
                <span aria-hidden="true">→</span>
              </Link>
            </div>
          </>
        ) : (
          <EmptyState>Belum ada artikel yang diterbitkan.</EmptyState>
        )}
      </section>

      {/* =====================================================
          PROJECTS
      ===================================================== */}
      <section className="mt-16 border-t border-stone-200 pt-12 sm:mt-20 sm:pt-16">
        <div className="mb-7 flex items-end justify-between gap-4 sm:mb-8">
          <div>
            <p className="mb-1 text-xs font-semibold tracking-widest text-stone-400 uppercase">
              Selected Works
            </p>

            <h2 className="text-2xl font-extrabold tracking-tight text-stone-950 sm:text-3xl">
              Proyek Terbaru
            </h2>

            <p className="mt-2 text-sm text-stone-500">
              Eksperimen, karya, dan hal-hal yang sedang dibangun.
            </p>
          </div>

          <Link
            href="/projects"
            className="hidden shrink-0 items-center gap-2 text-sm font-semibold text-stone-800 transition hover:text-stone-500 sm:inline-flex"
          >
            Lihat semua
            <span aria-hidden="true">→</span>
          </Link>
        </div>

        {home.latest_projects.length > 0 ? (
          <>
            <div className="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:gap-6">
              {home.latest_projects.map((project) => (
                <ProjectCard key={project.id} project={project} />
              ))}
            </div>

            <div className="mt-8 sm:hidden">
              <Link
                href="/projects"
                className="flex w-full items-center justify-center gap-2 rounded-xl border border-stone-200 bg-white px-5 py-3 text-sm font-semibold text-stone-800 transition hover:border-stone-400 hover:bg-stone-50"
              >
                Lihat Semua Proyek
                <span aria-hidden="true">→</span>
              </Link>
            </div>
          </>
        ) : (
          <EmptyState>Belum ada proyek yang ditampilkan.</EmptyState>
        )}
      </section>

      {/* =====================================================
          ABOUT CTA
      ===================================================== */}
      <section className="mt-16 sm:mt-20">
        <div className="relative overflow-hidden rounded-3xl border border-stone-200 bg-stone-100/70 px-6 py-10 text-center sm:px-12 sm:py-14">
          <div className="relative mx-auto max-w-xl">
            <p className="mb-3 text-xs font-semibold tracking-widest text-stone-400 uppercase">
              A Little About Me
            </p>

            <h2 className="text-2xl font-extrabold tracking-tight text-stone-950 sm:text-3xl">
              Setiap perjalanan punya cerita.
            </h2>

            <p className="mx-auto mt-4 max-w-lg font-serif text-sm leading-7 text-stone-600 sm:text-base">
              Blog ini adalah tempat saya menyimpan berbagai cerita, pengalaman, pemikiran, dan
              proses belajar. Bukan hanya tentang teknologi, tetapi juga tentang kehidupan dan
              perjalanan yang membentuk diri.
            </p>

            <Link
              href="/about"
              className="mt-7 inline-flex items-center gap-2 rounded-full bg-stone-900 px-6 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-stone-700"
            >
              Tentang Saya
              <span aria-hidden="true">→</span>
            </Link>
          </div>
        </div>
      </section>
    </main>
  );
}
