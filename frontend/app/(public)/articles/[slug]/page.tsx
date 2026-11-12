import type { Metadata } from 'next';
import Link from 'next/link';
import { notFound } from 'next/navigation';
import { getPost } from '@/lib/api/resources';
import { ApiError } from '@/lib/api/client';
import { ArticleContent } from '@/components/articles/article-content';
import { Comments } from '@/components/articles/comments';
import { SmartImage } from '@/components/ui/smart-image';
import { Suspense } from 'react';
import { RouteLoading } from '@/components/ui/route-loading';
type Props = { params: Promise<{ slug: string }> };
async function load(slug: string) {
  try {
    return await getPost(slug);
  } catch (error) {
    if (error instanceof ApiError && error.status === 404) notFound();
    throw error;
  }
}
export async function generateMetadata({ params }: Props): Promise<Metadata> {
  const { slug } = await params;
  const post = await load(slug);
  const canonical = `/articles/${post.slug}`;
  return {
    title: post.seo.title,
    description: post.seo.description || post.excerpt || undefined,
    alternates: { canonical },
    openGraph: {
      type: 'article',
      title: post.seo.title,
      description: post.seo.description || post.excerpt || undefined,
      url: canonical,
      images: post.cover_image ? [post.cover_image] : [],
      publishedTime: post.published_at || undefined,
    },
    twitter: {
      card: post.cover_image ? 'summary_large_image' : 'summary',
      title: post.seo.title,
      description: post.seo.description || post.excerpt || undefined,
      images: post.cover_image ? [post.cover_image] : [],
    },
  };
}
export default function ArticleDetailPage({ params }: Props) {
  return (
    <Suspense fallback={<RouteLoading variant="detail" />}>
      <ArticleDetailPageContent params={params} />
    </Suspense>
  );
}

async function ArticleDetailPageContent({ params }: Props) {
  const { slug } = await params;
  const post = await load(slug);
  return (
    <article className="mx-auto max-w-5xl px-4 py-12 font-sans sm:px-6 sm:py-16">
      <header className="mx-auto mb-10 max-w-3xl space-y-4 text-center sm:text-left">
        {post.category && (
          <Link
            href={`/categories/${post.category.slug}`}
            className="inline-flex items-center gap-1.5 rounded-md bg-stone-100 px-2.5 py-1 text-xs font-semibold tracking-wider text-stone-700 uppercase transition hover:bg-stone-200"
          >
            <span className="h-1.5 w-1.5 rounded-full bg-stone-500" />
            {post.category.name}
          </Link>
        )}
        <h1 className="text-3xl font-extrabold tracking-tight text-stone-900 sm:text-4xl lg:text-5xl lg:leading-tight">
          {post.title}
        </h1>
        {post.excerpt && (
          <p className="text-base leading-relaxed font-normal text-stone-600 sm:text-lg">
            {post.excerpt}
          </p>
        )}
        <div className="flex items-center gap-2 pt-2 text-xs font-medium text-stone-400">
          {post.published_at && (
            <>
              <svg
                className="h-4 w-4 text-stone-400"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                strokeWidth="2"
              >
                <path
                  strokeLinecap="round"
                  strokeLinejoin="round"
                  d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2Z"
                />
              </svg>
              <time dateTime={post.published_at}>
                {new Date(post.published_at).toLocaleDateString('id-ID', {
                  day: 'numeric',
                  month: 'long',
                  year: 'numeric',
                })}
              </time>
            </>
          )}
          {post.published_at && <span>·</span>}
          <span>{post.reading_time} menit baca</span>
        </div>
      </header>
      {post.cover_image && (
        <div className="relative mb-12 h-[320px] max-h-[520px] overflow-hidden rounded-2xl bg-stone-100 shadow-sm sm:h-[420px]">
          <SmartImage
            src={post.cover_image}
            alt={post.title}
            fill
            sizes="(max-width: 1024px) 100vw, 1024px"
            className="object-cover"
          />
        </div>
      )}
      <div className="mx-auto max-w-4xl">
        <ArticleContent blocks={post.content || []} />
        {post.tags.length > 0 && (
          <div className="mt-12 flex flex-wrap gap-2 border-t border-stone-200/80 pt-6">
            {post.tags.map((tag) => (
              <Link
                key={tag.id}
                href={`/tags/${tag.slug}`}
                className="rounded-lg bg-stone-100 px-3 py-1 text-xs font-semibold text-stone-600 hover:bg-stone-200"
              >
                #{tag.name}
              </Link>
            ))}
          </div>
        )}
        <Comments
          comments={post.comments || []}
          count={post.comments_count || 0}
          slug={post.slug}
        />
      </div>
    </article>
  );
}
