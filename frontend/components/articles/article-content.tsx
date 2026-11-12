import type { ContentBlock } from '@/types/api';
import { sanitizeHtml } from '@/lib/sanitization/html';
import { SmartImage } from '@/components/ui/smart-image';
function CodeBlock({
  language,
  code,
  filename,
}: {
  language?: string | null;
  code?: string | null;
  filename?: string | null;
}) {
  return (
    <div className="not-prose my-6 overflow-hidden rounded-lg border border-stone-800 bg-stone-900 text-sm shadow-sm">
      <div className="flex items-center justify-between gap-4 border-b border-stone-700/80 bg-stone-800/60 px-4 py-2">
        <div className="flex min-w-0 items-center gap-2 text-xs text-stone-300">
          {filename && (
            <>
              <span className="truncate font-medium text-stone-100">{filename}</span>
              <span className="text-stone-600">·</span>
            </>
          )}
          <span className="tracking-wide text-stone-400 uppercase">{language || 'Text'}</span>
        </div>
        <CopyButton code={code || ''} />
      </div>
      <pre className="overflow-x-auto p-4 leading-relaxed">
        <code>{code}</code>
      </pre>
    </div>
  );
}
function CopyButton({ code }: { code: string }) {
  return (
    <button
      type="button"
      className="copy-code shrink-0 rounded-md px-2.5 py-1 text-xs font-medium text-stone-300 transition hover:bg-stone-700 hover:text-white"
      onClick={async (e) => {
        const button = e.currentTarget;
        try {
          await navigator.clipboard.writeText(code);
          button.textContent = 'Copied!';
          setTimeout(() => {
            button.textContent = 'Copy';
          }, 1500);
        } catch {
          button.textContent = 'Gagal';
        }
      }}
    >
      Copy
    </button>
  );
}
export function ArticleContent({ blocks }: { blocks: ContentBlock[] }) {
  if (!blocks.length)
    return <p className="text-stone-500 italic">Artikel ini belum memiliki konten.</p>;
  return (
    <div className="article-content font-serif text-base leading-relaxed text-stone-800">
      {blocks.map((block, index) => {
        const data = block.data as Record<string, unknown>;
        switch (block.type) {
          case 'text': {
            const html = typeof data.content === 'string' ? data.content : '';
            return html ? (
              <div
                key={index}
                className="prose-block"
                dangerouslySetInnerHTML={{ __html: sanitizeHtml(html) }}
              />
            ) : null;
          }
          case 'image': {
            const url = typeof data.url === 'string' ? data.url : null;
            return url ? (
              <figure key={index} className="not-prose my-10">
                <SmartImage
                  src={url}
                  alt={typeof data.caption === 'string' ? data.caption : 'Gambar artikel'}
                  width={1200}
                  height={800}
                  className="h-auto w-full rounded-xl border border-stone-200/80 shadow-sm"
                />
                {typeof data.caption === 'string' && data.caption && (
                  <figcaption className="mt-2.5 text-center font-sans text-xs text-stone-500">
                    {data.caption}
                  </figcaption>
                )}
              </figure>
            ) : null;
          }
          case 'code':
            return (
              <CodeBlock
                key={index}
                language={typeof data.language === 'string' ? data.language : null}
                filename={typeof data.filename === 'string' ? data.filename : null}
                code={typeof data.code === 'string' ? data.code : ''}
              />
            );
          case 'quote':
            return typeof data.quote === 'string' && data.quote ? (
              <blockquote
                key={index}
                className="not-prose my-10 rounded-r-xl border-l-4 border-stone-800 bg-stone-50 px-6 py-5"
              >
                <p className="font-serif text-lg leading-relaxed text-stone-800 italic">
                  “{data.quote}”
                </p>
                {typeof data.author === 'string' && data.author && (
                  <footer className="mt-3 font-sans text-xs font-semibold tracking-wider text-stone-500 uppercase">
                    — {data.author}
                  </footer>
                )}
              </blockquote>
            ) : null;
          default:
            return null;
        }
      })}
    </div>
  );
}
