const ALLOWED_TAGS = new Set([
  'p',
  'br',
  'hr',
  'h1',
  'h2',
  'h3',
  'h4',
  'h5',
  'h6',
  'strong',
  'b',
  'em',
  'i',
  'u',
  's',
  'ul',
  'ol',
  'li',
  'blockquote',
  'a',
  'img',
  'figure',
  'figcaption',
  'pre',
  'code',
  'table',
  'thead',
  'tbody',
  'tr',
  'th',
  'td',
  'div',
  'span',
  'sup',
  'sub',
]);
const VOID_TAGS = new Set(['br', 'hr', 'img']);
const ALLOWED_ATTRS = new Set([
  'href',
  'src',
  'alt',
  'title',
  'width',
  'height',
  'target',
  'colspan',
  'rowspan',
  'class',
]);

function safeUrl(value: string): boolean {
  const normalized = value.trim().replace(/[\u0000-\u0020]+/g, '');
  return /^(https?:|mailto:|tel:|\/|#)/i.test(normalized) && !/^\/\//.test(normalized);
}
function escapeAttr(value: string): string {
  return value
    .replace(/&/g, '&amp;')
    .replace(/"/g, '&quot;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;');
}

/** Conservative SSR-safe HTML allowlist sanitizer for editor-generated content. */
export function sanitizeHtml(input: string | null | undefined): string {
  if (!input) return '';
  const html = input
    .replace(/<!--[\s\S]*?-->/g, '')
    .replace(
      /<(script|style|iframe|object|embed|form|svg|math|template)\b[^>]*>[\s\S]*?<\/\1\s*>/gi,
      '',
    )
    .replace(/<(script|style|iframe|object|embed|form|svg|math|template)\b[^>]*\/?>/gi, '');
  return html.replace(
    /<\/?([a-zA-Z][\w:-]*)([^>]*)>/g,
    (whole, rawTag: string, rawAttrs: string) => {
      const tag = rawTag.toLowerCase();
      if (!ALLOWED_TAGS.has(tag)) return '';
      const closing = /^<\//.test(whole);
      if (closing) return VOID_TAGS.has(tag) ? '' : `</${tag}>`;
      const attrs: string[] = [];
      rawAttrs.replace(
        /([\w:-]+)\s*=\s*(?:"([^"]*)"|'([^']*)'|([^\s>]+))/g,
        (
          _match,
          rawName: string,
          doubleValue?: string,
          singleValue?: string,
          bareValue?: string,
        ) => {
          const name = rawName.toLowerCase();
          const value = doubleValue ?? singleValue ?? bareValue ?? '';
          if (!ALLOWED_ATTRS.has(name) || name.startsWith('on')) return '';
          if ((name === 'href' || name === 'src') && !safeUrl(value)) return '';
          if (name === 'target' && value !== '_blank') return '';
          attrs.push(`${name}="${escapeAttr(name === 'target' ? '_blank' : value)}"`);
          return '';
        },
      );
      if (tag === 'a' && attrs.some((attr) => attr.startsWith('target=')))
        attrs.push('rel="noopener noreferrer"');
      return `<${tag}${attrs.length ? ` ${attrs.join(' ')}` : ''}${VOID_TAGS.has(tag) ? ' />' : '>'}`;
    },
  );
}
