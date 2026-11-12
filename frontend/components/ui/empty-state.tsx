import type { ReactNode } from 'react';
export function EmptyState({ children }: { children: ReactNode }) {
  return (
    <div className="col-span-full rounded-xl border border-dashed border-stone-200 p-8 text-center">
      <p className="text-sm text-stone-500">{children}</p>
    </div>
  );
}
