type RouteLoadingProps = {
  variant?: 'listing' | 'detail' | 'profile';
};

export function RouteLoading({ variant = 'listing' }: RouteLoadingProps) {
  if (variant === 'detail') {
    return (
      <div className="mx-auto max-w-5xl px-4 py-12 sm:px-6 sm:py-16">
        <div className="mx-auto max-w-3xl animate-pulse space-y-6">
          <div className="h-5 w-28 rounded bg-stone-200" />
          <div className="space-y-3">
            <div className="h-10 w-full rounded bg-stone-200" />
            <div className="h-10 w-4/5 rounded bg-stone-200" />
          </div>
          <div className="h-4 w-2/3 rounded bg-stone-200" />
          <div className="h-4 w-32 rounded bg-stone-200" />
          <div className="mt-10 h-[320px] rounded-2xl bg-stone-200 sm:h-[420px]" />
          <div className="space-y-4 pt-6">
            <div className="h-4 w-full rounded bg-stone-200" />
            <div className="h-4 w-full rounded bg-stone-200" />
            <div className="h-4 w-5/6 rounded bg-stone-200" />
            <div className="h-4 w-full rounded bg-stone-200" />
            <div className="h-4 w-3/4 rounded bg-stone-200" />
          </div>
        </div>
      </div>
    );
  }

  if (variant === 'profile') {
    return (
      <div className="mx-auto max-w-3xl px-4 py-8 sm:px-6 sm:py-16">
        <div className="animate-pulse overflow-hidden rounded-2xl border border-stone-200/80 bg-white p-6 sm:p-10">
          <div className="flex items-center gap-6 border-b border-stone-100 pb-8">
            <div className="h-28 w-28 shrink-0 rounded-full bg-stone-200" />
            <div className="flex-1 space-y-3">
              <div className="h-3 w-32 rounded bg-stone-200" />
              <div className="h-7 w-48 max-w-full rounded bg-stone-200" />
              <div className="h-4 w-40 rounded bg-stone-200" />
            </div>
          </div>
          <div className="space-y-4 py-8">
            <div className="h-5 w-36 rounded bg-stone-200" />
            <div className="h-4 w-full rounded bg-stone-200" />
            <div className="h-4 w-full rounded bg-stone-200" />
            <div className="h-4 w-4/5 rounded bg-stone-200" />
          </div>
        </div>
      </div>
    );
  }

  return (
    <div className="mx-auto max-w-5xl px-4 py-12 sm:px-6 sm:py-16">
      <div className="animate-pulse space-y-8">
        <header className="space-y-3 border-b border-stone-200 pb-8">
          <div className="h-3 w-32 rounded bg-stone-200" />
          <div className="h-8 w-64 max-w-full rounded bg-stone-200" />
          <div className="h-4 w-2/3 rounded bg-stone-200" />
        </header>
        <div className="flex flex-wrap gap-2">
          {[1, 2, 3, 4].map((item) => (
            <div key={item} className="h-8 w-24 rounded-lg bg-stone-200" />
          ))}
        </div>
        <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
          {[1, 2, 3, 4, 5, 6].map((item) => (
            <div
              key={item}
              className="h-56 rounded-xl border border-stone-200 bg-stone-100"
            />
          ))}
        </div>
      </div>
    </div>
  );
}
