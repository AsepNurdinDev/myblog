import Link from 'next/link';
import type { Project } from '@/types/api';
import { SmartImage } from '@/components/ui/smart-image';
import { safeExternalUrl } from '@/lib/utils/url';

export function ProjectCard({ project }: { project: Project }) {
  const completed = project.status === 'completed';
  const githubUrl = safeExternalUrl(project.github_url);
  const projectUrl = safeExternalUrl(project.url);

  const statusLabel =
    project.status === 'completed'
      ? 'Selesai'
      : project.status === 'ongoing'
        ? 'Berlangsung'
        : 'Diarsipkan';

  return (
    <article className="group flex h-full flex-col overflow-hidden rounded-2xl border border-stone-200/80 bg-white transition-all duration-300 hover:-translate-y-1 hover:border-stone-300 hover:shadow-lg hover:shadow-stone-200/40">
      {/* Thumbnail */}
      <Link
        href={`/projects/${project.slug}`}
        className="relative block aspect-[16/10] w-full overflow-hidden bg-stone-100"
      >
        {project.thumbnail ? (
          <SmartImage
            src={project.thumbnail}
            alt={project.title}
            fill
            sizes="(max-width: 640px) 100vw, (max-width: 1024px) 50vw, 33vw"
            className="object-cover transition-transform duration-500 group-hover:scale-105"
          />
        ) : (
          <div className="flex h-full items-center justify-center bg-stone-100">
            <span className="text-4xl font-bold text-stone-300">
              {project.title.charAt(0).toUpperCase()}
            </span>
          </div>
        )}
      </Link>

      {/* Content */}
      <div className="flex flex-1 flex-col p-4 sm:p-5">
        {/* Status and year */}
        <div className="mb-3 flex items-center justify-between gap-2">
          {project.status && (
            <span
              className={`inline-flex items-center gap-1.5 rounded-full border px-2.5 py-1 text-[10px] font-semibold ${
                completed
                  ? 'border-emerald-200 bg-emerald-50 text-emerald-700'
                  : 'border-stone-200 bg-stone-100 text-stone-600'
              }`}
            >
              <span
                className={`h-1.5 w-1.5 rounded-full ${
                  completed ? 'bg-emerald-500' : 'bg-stone-400'
                }`}
              />
              {statusLabel}
            </span>
          )}

          {project.started_at && (
            <span className="text-xs font-medium text-stone-400">
              {new Date(project.started_at).getFullYear()}
            </span>
          )}
        </div>

        {/* Title */}
        <h3 className="mb-2 line-clamp-2 text-base leading-snug font-bold tracking-tight text-stone-900 transition-colors group-hover:text-stone-600 sm:text-lg">
          <Link href={`/projects/${project.slug}`}>{project.title}</Link>
        </h3>

        {/* Description */}
        {project.description && (
          <p className="mb-4 line-clamp-3 text-xs leading-relaxed text-stone-600 sm:text-sm">
            {project.description}
          </p>
        )}

        {/* Skills */}
        {project.skills.length > 0 && (
          <div className="mb-5 flex flex-wrap gap-1.5">
            {project.skills.slice(0, 4).map((skill) => (
              <span
                key={skill.id}
                className="rounded-md bg-stone-100 px-2 py-1 text-[10px] font-medium text-stone-600"
              >
                {skill.name}
              </span>
            ))}

            {project.skills.length > 4 && (
              <span className="rounded-md bg-stone-100 px-2 py-1 text-[10px] font-medium text-stone-500">
                +{project.skills.length - 4}
              </span>
            )}
          </div>
        )}

        {/* Footer */}
        <div className="mt-auto flex items-center justify-between gap-2 border-t border-stone-100 pt-4">
          <Link
            href={`/projects/${project.slug}`}
            className="inline-flex items-center gap-1.5 text-xs font-semibold text-stone-900 transition-colors hover:text-stone-500"
          >
            Detail Project
            <span className="transition-transform group-hover:translate-x-1">→</span>
          </Link>

          <div className="flex items-center gap-3">
            {githubUrl && (
              <a
                href={githubUrl}
                target="_blank"
                rel="noopener noreferrer"
                className="text-xs font-medium text-stone-400 transition-colors hover:text-stone-900"
                title="Source Code"
              >
                GitHub
              </a>
            )}

            {projectUrl && (
              <a
                href={projectUrl}
                target="_blank"
                rel="noopener noreferrer"
                className="text-xs font-medium text-stone-400 transition-colors hover:text-stone-900"
                title="Demo / Website"
                aria-label="Buka demo project"
              >
                ↗
              </a>
            )}
          </div>
        </div>
      </div>
    </article>
  );
}
