import { getProjects } from '@/lib/api/resources';
import { ProjectCard } from '@/components/projects/project-card';
import { Pagination } from '@/components/ui/pagination';
import { EmptyState } from '@/components/ui/empty-state';
import { Suspense } from 'react';
import { RouteLoading } from '@/components/ui/route-loading';
export const metadata = {
  title: 'Project',
  description: 'Kumpulan project, aplikasi, dan sistem backend yang pernah saya bangun.',
};
type Props = { searchParams: Promise<{ page?: string }> };
export default function ProjectsPage({ searchParams }: Props) {
  return (
    <Suspense fallback={<RouteLoading variant="listing" />}>
      <ProjectsPageContent searchParams={searchParams} />
    </Suspense>
  );
}

async function ProjectsPageContent({ searchParams }: Props) {
  const params = await searchParams;
  const page = Math.max(1, Number(params.page) || 1);
  const projects = await getProjects(page);
  return (
    <div className="mx-auto max-w-4xl px-4 py-12 font-sans sm:px-6 sm:py-16">
      <header className="mb-8 space-y-2 border-b border-stone-200 pb-8">
        <div className="flex items-center gap-2">
          <span className="inline-block h-2.5 w-2.5 animate-pulse rounded-full bg-emerald-500" />
          <span className="text-xs font-semibold tracking-wider text-stone-500 uppercase">
            Portfolio & Showcase
          </span>
        </div>
        <h1 className="text-3xl font-extrabold tracking-tight text-stone-900 sm:text-4xl">
          Project & Portfolio
        </h1>
        <p className="max-w-2xl text-sm leading-relaxed text-stone-600">
          Kumpulan aplikasi web, microservices, arsitektur backend, dan tools open-source yang
          pernah saya rancang dan kembangkan.
        </p>
      </header>
      <section className="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
        {projects.data.length ? (
          projects.data.map((project) => <ProjectCard key={project.id} project={project} />)
        ) : (
          <EmptyState>Belum ada project yang ditampilkan.</EmptyState>
        )}
      </section>
      <Pagination
        current={projects.meta.current_page}
        last={projects.meta.last_page}
        base="/projects"
      />
    </div>
  );
}
