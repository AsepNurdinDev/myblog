@props(['project'])

<article class="group flex flex-col overflow-hidden rounded-xl border border-stone-200/80 bg-white transition-all duration-300 hover:border-stone-300 hover:shadow-md">
    {{-- Thumbnail Image --}}
    @if ($project->thumbnail)
        <a href="{{ route('projects.show', $project) }}" class="block aspect-[16/9] w-full overflow-hidden bg-stone-100">
            <img
                src="{{ Storage::url($project->thumbnail) }}"
                alt="{{ $project->title }}"
                loading="lazy"
                class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-[1.03]"
            >
        </a>
    @endif

    <div class="flex flex-1 flex-col p-5">
        {{-- Status Badge & Date --}}
        <div class="mb-3 flex items-center justify-between gap-2 text-xs">
            @if ($project->status)
                @php
                    $statusValue = is_object($project->status) ? $project->status->value : $project->status;
                    $isCompleted = strtolower($statusValue) === 'completed' || strtolower($statusValue) === 'selesai';
                @endphp
                <span class="inline-flex items-center gap-1.5 rounded-md px-2 py-0.5 font-medium {{ $isCompleted ? 'bg-emerald-50 text-emerald-700 border border-emerald-200/50' : 'bg-stone-100 text-stone-600 border border-stone-200/50' }}">
                    <span class="h-1.5 w-1.5 rounded-full {{ $isCompleted ? 'bg-emerald-500' : 'bg-stone-400' }}"></span>
                    {{ ucfirst($statusValue) }}
                </span>
            @endif

            @if ($project->started_at)
                <span class="text-stone-400">
                    {{ $project->started_at->translatedFormat('M Y') }}
                </span>
            @endif
        </div>

        {{-- Title --}}
        <h3 class="mb-2 text-lg font-bold leading-snug tracking-tight text-stone-900 transition group-hover:text-stone-600">
            <a href="{{ route('projects.show', $project) }}" class="focus:outline-none">
                {{ $project->title }}
            </a>
        </h3>

        {{-- Short Description --}}
        @if ($project->description)
            <p class="mb-4 line-clamp-2 text-xs leading-relaxed text-stone-600">
                {{ $project->description }}
            </p>
        @endif

        {{-- Tech Stack / Skills --}}
        @if ($project->relationLoaded('skills') && $project->skills->isNotEmpty())
            <div class="mb-5 flex flex-wrap gap-1">
                @foreach ($project->skills as $skill)
                    <span class="rounded bg-stone-100 px-2 py-0.5 text-[10px] font-medium text-stone-600">
                        {{ $skill->name }}
                    </span>
                @endforeach
            </div>
        @endif

        {{-- Action Links --}}
        <div class="mt-auto flex items-center justify-between border-t border-stone-100 pt-4 text-xs font-semibold">
            <a
                href="{{ route('projects.show', $project) }}"
                class="inline-flex items-center gap-1 text-stone-900 transition hover:text-stone-600 group-hover:translate-x-0.5"
            >
                Detail Project
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-3.5 w-3.5 transition-transform group-hover:translate-x-1">
                    <path fill-rule="evenodd" d="M3 10a.75.75 0 0 1 .75-.75h10.638l-3.96-4.158a.75.75 0 1 1 1.08-1.04l5.25 5.5a.75.75 0 0 1 0 1.04l-5.25 5.5a.75.75 0 1 1-1.08-1.04l3.96-4.158H3.75A.75.75 0 0 1 3 10Z" clip-rule="evenodd" />
                </svg>
            </a>

            <div class="flex items-center gap-2">
                @if ($project->github_url)
                    <a href="{{ $project->github_url }}" target="_blank" rel="noopener noreferrer" class="text-stone-400 hover:text-stone-800" title="Source Code">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="h-4 w-4 fill-current">
                            <path d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.53 1.032 1.53 1.032.892 1.53 2.341 1.088 2.91.833.091-.647.35-1.088.636-1.339-2.221-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0 1 12 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.31.678.921.678 1.856 0 1.339-.012 2.419-.012 2.749 0 .268.18.58.688.482A10.02 10.02 0 0 0 22 12.017C22 6.484 17.522 2 12 2Z" />
                        </svg>
                    </a>
                @endif
                @if ($project->url)
                    <a href="{{ $project->url }}" target="_blank" rel="noopener noreferrer" class="text-stone-400 hover:text-stone-800" title="Demo / Web">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                        </svg>
                    </a>
                @endif
            </div>
        </div>
    </div>
</article>