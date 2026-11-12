export default function Loading() {
  return (
    <div className="mx-auto max-w-5xl px-4 py-16 sm:px-6">
      <div className="animate-pulse space-y-5">
        <div className="h-8 w-1/3 rounded bg-stone-200" />
        <div className="h-4 w-2/3 rounded bg-stone-200" />
        <div className="grid grid-cols-1 gap-6 sm:grid-cols-3">
          {[1, 2, 3].map((item) => (
            <div key={item} className="h-64 rounded-2xl border border-stone-200 bg-white" />
          ))}
        </div>
      </div>
    </div>
  );
}
