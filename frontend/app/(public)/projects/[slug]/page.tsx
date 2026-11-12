import type { Metadata } from 'next';
import { notFound } from 'next/navigation';
import { ApiError } from '@/lib/api/client';
import { getProject } from '@/lib/api/resources';
import { sanitizeHtml } from '@/lib/sanitization/html';
import { SmartImage } from '@/components/ui/smart-image';
import { safeExternalUrl } from '@/lib/utils/url';
import { Suspense } from 'react';
import { RouteLoading } from '@/components/ui/route-loading';
type Props = { params: Promise<{ slug: string }> };
async function load(slug: string) {
  try {
    return await getProject(slug);
  } catch (error) {
    if (error instanceof ApiError && error.status === 404) notFound();
    throw error;
  }
}
export async function generateMetadata({ params }: Props): Promise<Metadata> {
  const { slug } = await params;
  const project = await load(slug);
  return {
    title: project.title,
    description: project.description || project.title,
    alternates: { canonical: `/projects/${project.slug}` },
    openGraph: {
      type: 'website',
      title: project.title,
      description: project.description || undefined,
      images: project.thumbnail ? [project.thumbnail] : [],
    },
    twitter: {
      card: project.thumbnail ? 'summary_large_image' : 'summary',
      title: project.title,
      images: project.thumbnail ? [project.thumbnail] : [],
    },
  };
}
export default function ProjectDetailPage({ params }: Props) {
  return (
    <Suspense fallback={<RouteLoading variant="detail" />}>
      <ProjectDetailPageContent params={params} />
    </Suspense>
  );
}

async function ProjectDetailPageContent({ params }: Props) {
  const { slug } = await params;
  const project = await load(slug);
  const completed = project.status === 'completed';
  return (
    <article className="mx-auto max-w-5xl px-4 py-12 font-sans sm:px-6 sm:py-16">
      <header className="mx-auto mb-10 max-w-4xl space-y-4">
        <div className="flex flex-wrap items-center gap-3">
          {project.status && (
            <span
              className={`inline-flex items-center gap-1.5 rounded-md border px-2.5 py-1 text-xs font-semibold tracking-wider uppercase ${completed ? 'border-emerald-200/60 bg-emerald-50 text-emerald-700' : 'border-stone-200/60 bg-stone-100 text-stone-700'}`}
            >
              <span
                className={`h-1.5 w-1.5 rounded-full ${completed ? 'bg-emerald-500' : 'bg-stone-500'}`}
              />
              {project.status}
            </span>
          )}
          {project.started_at && (
            <span className="text-xs font-medium text-stone-400">
              {new Date(project.started_at).toLocaleDateString('id-ID', {
                month: 'short',
                year: 'numeric',
              })}
              {project.completed_at
                ? ` – ${new Date(project.completed_at).toLocaleDateString('id-ID', { month: 'short', year: 'numeric' })}`
                : ' – Sekarang'}
            </span>
          )}
        </div>
        <h1 className="text-3xl font-extrabold tracking-tight text-stone-900 sm:text-4xl">
          {project.title}
        </h1>
        {project.description && (
          <p className="text-base leading-relaxed font-normal text-stone-600 sm:text-lg">
            {project.description}
          </p>
        )}
        {safeExternalUrl(project.url) || safeExternalUrl(project.github_url) ? (
          <div className="flex flex-wrap gap-3 pt-3">
            {safeExternalUrl(project.url) && (
              <a
                href={safeExternalUrl(project.url)!}
                target="_blank"
                rel="noopener noreferrer"
                className="inline-flex items-center gap-2 rounded-xl bg-stone-900 px-5 py-2.5 text-xs font-semibold text-white shadow-sm transition hover:bg-stone-700"
              >
                Kunjungi Situs ↗
              </a>
            )}
            {safeExternalUrl(project.github_url) && (
              <a
                href={safeExternalUrl(project.github_url)!}
                target="_blank"
                rel="noopener noreferrer"
                className="inline-flex items-center gap-2 rounded-xl border border-stone-200/90 bg-white px-5 py-2.5 text-xs font-semibold text-stone-700 shadow-sm transition hover:border-stone-400 hover:bg-stone-50 hover:text-stone-900"
              >
                Lihat Repositori
              </a>
            )}
          </div>
        ) : null}
      </header>
      {project.thumbnail && (
        <div className="relative mb-12 h-[320px] max-h-[520px] overflow-hidden rounded-2xl bg-stone-100 shadow-sm sm:h-[420px]">
          <SmartImage
            src={project.thumbnail}
            alt={project.title}
            fill
            sizes="(max-width: 1024px) 100vw, 1024px"
            className="object-cover"
          />
        </div>
      )}
      <div className="mx-auto max-w-4xl">
        {project.content && (
          <div
            className="article-content font-serif"
            dangerouslySetInnerHTML={{ __html: sanitizeHtml(project.content) }}
          />
        )}
        {project.skills.length > 0 && (
          <div className="mt-12 border-t border-stone-200/80 pt-6 font-sans">
            <h2 className="mb-3 text-xs font-bold tracking-wider text-stone-400 uppercase">
              Teknologi & Tech Stack
            </h2>
            <div className="flex flex-wrap gap-2">
              {project.skills.map((skill) => (
                <span
                  key={skill.id}
                  className="rounded-lg border border-stone-200/80 bg-stone-100/70 px-3 py-1 text-xs font-semibold text-stone-700"
                >
                  {skill.name}
                </span>
              ))}
            </div>
          </div>
        )}
      </div>
    </article>
  );
}
