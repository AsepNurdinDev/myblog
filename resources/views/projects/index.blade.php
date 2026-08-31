@extends('layouts.app')

@section('title', 'Project — ' . config('app.name', 'Blog'))
@section('meta_description', 'Kumpulan project, aplikasi, dan sistem backend yang pernah saya bangun.')

@section('content')
    <div class="mx-auto max-w-4xl px-4 py-12 sm:px-6 sm:py-16 font-sans">

        {{-- Header Section --}}
        <header class="mb-8 space-y-2 border-b border-stone-200 pb-8">
            <div class="flex items-center gap-2">
                <span class="inline-block h-2.5 w-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                <span class="text-xs font-semibold uppercase tracking-wider text-stone-500">Portfolio & Showcase</span>
            </div>

            <h1 class="text-3xl font-extrabold tracking-tight text-stone-900 sm:text-4xl">
                Project & Portfolio
            </h1>
            
            <p class="max-w-2xl text-sm leading-relaxed text-stone-600">
                Kumpulan aplikasi web, microservices, arsitektur backend, dan tools open-source yang pernah saya rancang dan kembangkan.
            </p>
        </header>

        {{-- Interactive Search Section --}}
        <section class="mb-10">
            <form action="{{ route('projects.index') }}" method="GET" class="relative max-w-xl">
                <div class="relative flex items-center">
                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Cari project, tech stack, atau kata kunci..."
                        class="w-full rounded-xl border border-stone-200/90 bg-white py-3 pl-11 pr-24 text-sm text-stone-800 placeholder-stone-400 shadow-sm transition-all focus:border-stone-400 focus:outline-none focus:ring-1 focus:ring-stone-400"
                    >
                    <div class="pointer-events-none absolute left-3.5 flex items-center justify-center text-stone-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>

                    @if (request('search'))
                        <a
                            href="{{ route('projects.index') }}"
                            class="absolute right-20 text-xs font-semibold text-stone-400 hover:text-stone-600"
                        >
                            Reset
                        </a>
                    @endif

                    <button
                        type="submit"
                        class="absolute right-1.5 rounded-lg bg-stone-900 px-4 py-1.5 text-xs font-semibold text-white transition hover:bg-stone-700"
                    >
                        Cari
                    </button>
                </div>
            </form>

            @if (request('search'))
                <p class="mt-3 text-xs text-stone-500">
                    Menampilkan hasil pencarian project untuk kata kunci: <span class="font-semibold text-stone-800">"{{ request('search') }}"</span>
                </p>
            @endif
        </section>

        {{-- Projects Grid --}}
        <section class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @forelse ($projects as $project)
                <x-project-card :project="$project" />
            @empty
                <div class="col-span-full rounded-2xl border border-dashed border-stone-200/90 bg-white p-10 text-center shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-10 w-10 text-stone-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" />
                    </svg>
                    <p class="mt-3 text-sm font-medium text-stone-600">
                        {{ request('search') ? 'Tidak ada project yang cocok dengan pencarian Anda.' : 'Belum ada project yang ditampilkan.' }}
                    </p>
                    @if (request('search'))
                        <a href="{{ route('projects.index') }}" class="mt-4 inline-block text-xs font-semibold text-stone-900 underline hover:text-stone-600">
                            Tampilkan semua project
                        </a>
                    @endif
                </div>
            @endforelse
        </section>

        {{-- Pagination --}}
        @if ($projects->hasPages())
            <div class="mt-12 flex justify-center border-t border-stone-100 pt-6">
                {{ $projects->links() }}
            </div>
        @endif

    </div>
@endsection