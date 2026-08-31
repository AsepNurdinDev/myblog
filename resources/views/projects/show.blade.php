@extends('layouts.app')

@section('title', $project->title . ' — ' . config('app.name', 'Blog'))
@section('meta_description', $project->description ? Str::limit(strip_tags($project->description), 155) : $project->title)

@section('content')
    <article class="mx-auto max-w-5xl px-4 py-12 sm:px-6 sm:py-16 font-sans">

        {{-- Header Section --}}
        <header class="mx-auto max-w-4xl mb-10 space-y-4">
            <div class="flex flex-wrap items-center gap-3">
                @if ($project->status)
                    @php
                        $statusValue = is_object($project->status) ? $project->status->value : $project->status;
                        $isCompleted = strtolower($statusValue) === 'completed' || strtolower($statusValue) === 'selesai';
                    @endphp
                    <span class="inline-flex items-center gap-1.5 rounded-md px-2.5 py-1 text-xs font-semibold uppercase tracking-wider {{ $isCompleted ? 'bg-emerald-50 text-emerald-700 border border-emerald-200/60' : 'bg-stone-100 text-stone-700 border border-stone-200/60' }}">
                        <span class="h-1.5 w-1.5 rounded-full {{ $isCompleted ? 'bg-emerald-500' : 'bg-stone-500' }}"></span>
                        {{ ucfirst($statusValue) }}
                    </span>
                @endif

                @if ($project->started_at)
                    <span class="flex items-center gap-1.5 text-xs font-medium text-stone-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <time datetime="{{ $project->started_at->toDateString() }}">
                            {{ $project->started_at->translatedFormat('M Y') }}
                        </time>
                        @if ($project->completed_at)
                            &ndash;
                            <time datetime="{{ $project->completed_at->toDateString() }}">
                                {{ $project->completed_at->translatedFormat('M Y') }}
                            </time>
                        @else
                            &ndash; Sekarang
                        @endif
                    </span>
                @endif
            </div>

            <h1 class="text-3xl font-extrabold tracking-tight text-stone-900 sm:text-4xl lg:text-5xl lg:leading-tight">
                {{ $project->title }}
            </h1>

            @if ($project->description)
                <p class="text-base font-normal leading-relaxed text-stone-600 sm:text-lg">
                    {{ $project->description }}
                </p>
            @endif

            @if ($project->url || $project->github_url)
                <div class="flex flex-wrap gap-3 pt-3">
                    @if ($project->url)
                        <a
                            href="{{ $project->url }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="inline-flex items-center gap-2 rounded-xl bg-stone-900 px-5 py-2.5 text-xs font-semibold text-white transition hover:bg-stone-700 shadow-sm"
                        >
                            <span>Kunjungi Situs</span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                            </svg>
                        </a>
                    @endif

                    @if ($project->github_url)
                        <a
                            href="{{ $project->github_url }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="inline-flex items-center gap-2 rounded-xl border border-stone-200/90 bg-white px-5 py-2.5 text-xs font-semibold text-stone-700 transition hover:border-stone-400 hover:bg-stone-50 hover:text-stone-900 shadow-sm"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="h-4 w-4 fill-current">
                                <path d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.53 1.032 1.53 1.032.892 1.53 2.341 1.088 2.91.833.091-.647.35-1.088.636-1.339-2.221-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0 1 12 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.31.678.921.678 1.856 0 1.339-.012 2.419-.012 2.749 0 .268.18.58.688.482A10.02 10.02 0 0 0 22 12.017C22 6.484 17.522 2 12 2Z" />
                            </svg>
                            <span>Lihat Repositori</span>
                        </a>
                    @endif
                </div>
            @endif
        </header>

        {{-- Thumbnail Image --}}
        @if ($project->thumbnail)
            <div class="mb-12 overflow-hidden rounded-2xl bg-stone-100 shadow-sm max-h-[520px]">
                <img
                    src="{{ Storage::url($project->thumbnail) }}"
                    alt="{{ $project->title }}"
                    class="h-full w-full object-cover"
                >
            </div>
        @endif

        {{-- Content Area --}}
        <div class="mx-auto max-w-4xl">
            @if ($project->content)
                <div class="article-content prose prose-stone max-w-none font-serif text-base leading-relaxed text-stone-800 lg:prose-lg">
                    {!! $project->content !!}
                </div>
            @endif

            {{-- Tech Stack & Skills --}}
            @if ($project->skills->isNotEmpty())
                <div class="mt-12 border-t border-stone-200/80 pt-6 font-sans">
                    <h2 class="mb-3 text-xs font-bold uppercase tracking-wider text-stone-400">Teknologi & Tech Stack</h2>
                    <div class="flex flex-wrap gap-2">
                        @foreach ($project->skills as $skill)
                            <span class="rounded-lg border border-stone-200/80 bg-stone-100/70 px-3 py-1 text-xs font-semibold text-stone-700">
                                {{ $skill->name }}
                            </span>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

    </article>
@endsection