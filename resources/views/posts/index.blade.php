@extends('layouts.app')

@section('title', 'Catatan — ' . config('app.name', 'Blog'))
@section('meta_description', 'Kumpulan catatan, tutorial, dan pemikiran seputar programming dan kehidupan sehari-hari.')

@section('content')
    <div class="mx-auto max-w-4xl px-4 py-12 sm:px-6 sm:py-16 font-sans">

        {{-- Header Section --}}
        <header class="mb-8 space-y-2 border-b border-stone-200 pb-8">
            <div class="flex items-center gap-2">
                <span class="inline-block h-2.5 w-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                <span class="text-xs font-semibold uppercase tracking-wider text-stone-500">Journal & Articles</span>
            </div>

            <h1 class="text-3xl font-extrabold tracking-tight text-stone-900 sm:text-4xl">
                Catatan & Artikel
            </h1>
            
            <p class="max-w-2xl text-sm leading-relaxed text-stone-600">
                Tulisan tentang software engineering, arsitektur sistem, tutorial teknis, serta catatan eksplorasi teknologi sehari-hari.
            </p>
        </header>

        {{-- Interactive Search Section --}}
        <section class="mb-10">
            <form action="{{ route('posts.index') }}" method="GET" class="relative max-w-xl">
                <div class="relative flex items-center">
                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Cari artikel, topik, atau kata kunci..."
                        class="w-full rounded-xl border border-stone-200/90 bg-white py-3 pl-11 pr-24 text-sm text-stone-800 placeholder-stone-400 shadow-sm transition-all focus:border-stone-400 focus:outline-none focus:ring-1 focus:ring-stone-400"
                    >
                    <div class="pointer-events-none absolute left-3.5 flex items-center justify-center text-stone-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>

                    @if (request('search'))
                        <a
                            href="{{ route('posts.index') }}"
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
                    Menampilkan hasil pencarian untuk kata kunci: <span class="font-semibold text-stone-800">"{{ request('search') }}"</span>
                </p>
            @endif
        </section>

        {{-- Posts Grid --}}
        <section class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @forelse ($posts as $post)
                <x-post-card :post="$post" />
            @empty
                <div class="col-span-full rounded-2xl border border-dashed border-stone-200/90 bg-white p-10 text-center shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-10 w-10 text-stone-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                    </svg>
                    <p class="mt-3 text-sm font-medium text-stone-600">
                        {{ request('search') ? 'Tidak ada artikel yang cocok dengan pencarian Anda.' : 'Belum ada artikel yang diterbitkan.' }}
                    </p>
                    @if (request('search'))
                        <a href="{{ route('posts.index') }}" class="mt-4 inline-block text-xs font-semibold text-stone-900 underline hover:text-stone-600">
                            Tampilkan semua artikel
                        </a>
                    @endif
                </div>
            @endforelse
        </section>

        {{-- Pagination --}}
        @if ($posts->hasPages())
            <div class="mt-12 flex justify-center border-t border-stone-100 pt-6">
                {{ $posts->links() }}
            </div>
        @endif

    </div>
@endsection