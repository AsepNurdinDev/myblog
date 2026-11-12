import type { Comment } from '@/types/api';
import { CommentForm } from './comment-form';
export function Comments({
  comments,
  count,
  slug,
}: {
  comments: Comment[];
  count: number;
  slug: string;
}) {
  return (
    <section id="comments" className="mt-16 border-t border-stone-200/80 pt-10 font-sans">
      <div className="flex items-center justify-between">
        <h2 className="text-2xl font-bold tracking-tight text-stone-900">
          Komentar <span className="text-sm font-semibold text-stone-400">({count})</span>
        </h2>
      </div>
      <div className="mt-8 space-y-4">
        {comments.length ? (
          comments.map((comment) => (
            <div
              key={comment.id}
              className="rounded-xl border border-stone-200/80 bg-white p-5 shadow-sm"
            >
              <div className="flex items-center justify-between border-b border-stone-100 pb-3">
                <p className="text-sm font-bold text-stone-900">{comment.name}</p>
                {comment.created_at && (
                  <time className="text-xs text-stone-400" dateTime={comment.created_at}>
                    {new Date(comment.created_at).toLocaleDateString('id-ID', {
                      day: 'numeric',
                      month: 'short',
                      year: 'numeric',
                    })}
                  </time>
                )}
              </div>
              <p className="mt-3 text-sm leading-relaxed whitespace-pre-wrap text-stone-700">
                {comment.content}
              </p>
            </div>
          ))
        ) : (
          <div className="rounded-xl border border-dashed border-stone-200/90 bg-stone-50/50 p-6 text-center">
            <p className="text-xs text-stone-500">
              Belum ada komentar. Jadilah yang pertama berkomentar!
            </p>
          </div>
        )}
      </div>
      <CommentForm slug={slug} />
    </section>
  );
}
