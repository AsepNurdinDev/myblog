export function SearchForm({
  value = '',
  action = '/articles',
  placeholder = 'Cari artikel, topik, atau kata kunci...',
}: {
  value?: string;
  action?: string;
  placeholder?: string;
}) {
  return (
    <form action={action} method="GET" className="relative max-w-xl">
      <div className="relative flex items-center">
        <input
          type="text"
          name="search"
          defaultValue={value}
          placeholder={placeholder}
          className="search-input"
        />
        <div className="pointer-events-none absolute left-3 flex items-center text-stone-400">
          <svg
            className="h-4 w-4 sm:h-5 sm:w-5"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            strokeWidth="2"
          >
            <path
              strokeLinecap="round"
              strokeLinejoin="round"
              d="m21 21-6-6m2-5a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z"
            />
          </svg>
        </div>
        <button
          type="submit"
          className="absolute right-1.5 rounded-lg bg-stone-900 px-3 py-1.5 text-xs font-semibold text-white hover:bg-stone-700"
        >
          Cari
        </button>
      </div>
    </form>
  );
}
