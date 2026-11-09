@extends('layouts.app')

@section('title', 'Catatan — ' . config('app.name', 'Blog'))
@section('meta_description', 'Kumpulan catatan, tutorial, dan pemikiran seputar programming.')

@section('content')
    <div class="mx-auto max-w-5xl px-4 py-8 sm:px-6 sm:py-16 font-sans">

        {{-- Header Section --}}
        <header class="mb-6 space-y-1.5 border-b border-stone-200 pb-6 sm:mb-8 sm:space-y-2 sm:pb-8">
            <div class="flex items-center gap-2">
                <span class="inline-block h-2.5 w-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                <span class="text-xs font-semibold uppercase tracking-wider text-stone-500">Journal & Articles</span>
            </div>

            <h1 class="text-2xl font-extrabold tracking-tight text-stone-900 sm:text-4xl">
                Catatan & Artikel
            </h1>

            <p class="max-w-2xl text-xs leading-relaxed text-stone-600 sm:text-sm">
                Tulisan tentang software engineering, arsitektur sistem, tutorial teknis, serta catatan eksplorasi teknologi sehari-hari.
            </p>
        </header>

        {{-- Search Section --}}
        <section class="mb-8">
            <form action="{{ route('posts.index') }}" method="GET" class="relative max-w-xl">
                <div class="relative flex items-center">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari artikel, topik, atau kata kunci..."
                        class="w-full rounded-xl border border-stone-200/90 bg-white py-2.5 pl-10 pr-20 text-xs sm:text-sm text-stone-800 placeholder-stone-400 shadow-sm transition focus:border-stone-400 focus:outline-none focus:ring-1 focus:ring-stone-400">
                    <div class="pointer-events-none absolute left-3 flex items-center text-stone-400">
                        <svg class="h-4 w-4 sm:h-5 sm:w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>

                    @if (request('search'))
                        <a href="{{ route('posts.index') }}" class="absolute right-16 text-xs font-semibold text-stone-400 hover:text-stone-600">
                            Reset
                        </a>
                    @endif

                    <button type="submit" class="absolute right-1.5 rounded-lg bg-stone-900 px-3 py-1.5 text-xs font-semibold text-white hover:bg-stone-700">
                        Cari
                    </button>
                </div>
            </form>

            @if (request('search'))
                <p class="mt-2 text-xs text-stone-500">
                    Menampilkan hasil pencarian untuk: <span class="font-semibold text-stone-800">"{{ request('search') }}"</span>
                </p>
            @endif
        </section>

        {{-- Categories Section --}}
        @if ($categories->isNotEmpty())
            <section class="mb-10">
                <h2 class="mb-3 text-lg font-extrabold tracking-tight text-stone-900 sm:text-2xl">Topik & Kategori</h2>
                <div class="flex flex-wrap gap-1.5 sm:gap-2">
                    @foreach ($categories as $category)
                        <a href="{{ route('categories.show', $category) }}"
                            class="rounded-lg border border-stone-200/80 bg-white px-3 py-1 text-xs font-semibold text-stone-700 transition hover:border-stone-400 hover:bg-stone-50">
                            {{ $category->name }}
                        </a>
                    @endforeach
                </div>
            </section>
        @endif

        {{-- Posts Grid (Default 3x6) --}}
        <section class="grid grid-cols-1 gap-4 sm:grid-cols-2 sm:gap-6 lg:grid-cols-3">
            @forelse ($posts as $post)
                <x-post-card :post="$post" />
            @empty
                <div class="col-span-full rounded-2xl border border-dashed border-stone-200/90 bg-white p-8 text-center shadow-sm">
                    <svg class="mx-auto h-8 w-8 text-stone-300 sm:h-10 sm:w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                    </svg>
                    <p class="mt-2 text-xs font-medium text-stone-600 sm:text-sm">
                        {{ request('search') ? 'Tidak ada artikel yang cocok dengan pencarian Anda.' : 'Belum ada artikel yang diterbitkan.' }}
                    </p>
                    @if (request('search'))
                        <a href="{{ route('posts.index') }}" class="mt-3 inline-block text-xs font-semibold text-stone-900 underline hover:text-stone-600">
                            Tampilkan semua artikel
                        </a>
                    @endif
                </div>
            @endforelse
        </section>

        {{-- Tombol Tampilkan Semua Post & Pagination --}}
        <div class="mt-10 flex flex-col items-center gap-4">
            @if (!$showAll && $posts instanceof \Illuminate\Pagination\LengthAwarePaginator && $posts->hasMorePages())
                <a href="{{ request()->fullUrlWithQuery(['all' => 1]) }}"
                    class="rounded-xl border border-stone-300 bg-white px-5 py-2.5 text-xs font-semibold text-stone-800 shadow-sm transition hover:bg-stone-50 active:scale-95">
                    Tampilkan Semua Artikel Tanpa Halaman
                </a>
            @endif

            @if (!$showAll && $posts instanceof \Illuminate\Pagination\LengthAwarePaginator && $posts->hasPages())
                <div class="w-full flex justify-center border-t border-stone-100 pt-6">
                    {{ $posts->links() }}
                </div>
            @endif
        </div>

    </div>
@endsection