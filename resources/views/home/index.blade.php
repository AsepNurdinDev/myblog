@extends('layouts.app')

@section('title', config('app.name', 'Personal Blog') . ' — Catatan, Journal & Project Portfolio')
@section('meta_description',
    $profile?->clean_bio ??
    'Ruang berbagi catatan teknis, jurnal perjalanan software
    engineering, dan portfolio proyek.')

@section('content')
    <div class="mx-auto max-w-5xl px-6 py-12 font-sans sm:py-16">

        {{-- Hero / Personal Branding Section --}}
        <section class="flex flex-col items-start gap-6 border-b border-stone-200 pb-12 sm:flex-row sm:items-center">
            @if ($profile?->avatar)
                <img src="{{ Storage::disk('public')->url($profile->avatar) }}" alt="{{ $profile->user->name ?? config('app.name') }}"
                    class="h-24 w-24 shrink-0 rounded-full border border-stone-200 object-cover shadow-sm">
            @else
                <div
                    class="flex h-24 w-24 shrink-0 items-center justify-center rounded-full bg-stone-900 font-sans text-3xl font-bold text-white shadow-sm">
                    {{ Str::of($profile?->user?->name ?? config('app.name', 'A'))->substr(0, 1) }}
                </div>
            @endif

            <div class="space-y-3">
                <div class="flex items-center gap-2">
                    <span class="text-xs font-semibold uppercase tracking-wider text-stone-500">Personal Blog &
                        Journal</span>
                </div>

                <h1 class="text-3xl font-extrabold tracking-tight text-stone-900 sm:text-4xl">
                    {{ $profile?->user?->name ?? config('app.name', 'Selamat Datang') }}
                </h1>

                @if ($profile?->headline)
                    <p class="text-sm font-semibold text-stone-700">{{ $profile->headline }}</p>
                @endif

                {{-- Narasi Personal Blog (Menggunakan clean_bio) --}}
                <p class="max-w-2xl font-serif text-base leading-relaxed text-stone-600">
                    {{ $profile?->clean_bio ?? 'Selamat datang di ruang digital saya. Tempat saya mendokumentasikan proses belajar, pengalaman membangun perangkat lunak, catatan arsitektur sistem, serta jurnal eksplorasi teknologi sehari-hari.' }}
                </p>
            </div>
        </section>

        {{-- Interactive Search Section --}}
        <section class="mt-10">
            <form action="{{ route('posts.index') }}" method="GET" class="relative max-w-xl">
                <div class="relative flex items-center">
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Cari artikel, topik, atau kata kunci..."
                        class="w-full rounded-xl border border-stone-200/90 bg-white py-3 pl-11 pr-24 text-sm text-stone-800 placeholder-stone-400 shadow-sm transition-all focus:border-stone-400 focus:outline-none focus:ring-1 focus:ring-stone-400">
                    <div class="pointer-events-none absolute left-3.5 flex items-center justify-center text-stone-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <button type="submit"
                        class="absolute right-1.5 rounded-lg bg-stone-900 px-4 py-1.5 text-xs font-semibold text-white transition hover:bg-stone-700">
                        Cari
                    </button>
                </div>
            </form>
        </section>

        {{-- Categories Section --}}
        @if ($categories->isNotEmpty())
            <section class="mt-16">
                <h2 class="mb-4 text-2xl font-extrabold tracking-tight text-stone-900">Topik & Kategori</h2>
                <div class="flex flex-wrap gap-2">
                    @foreach ($categories as $category)
                        <a href="{{ route('categories.show', $category) }}"
                            class="rounded-lg border border-stone-200/80 bg-white px-3.5 py-1.5 text-xs font-semibold text-stone-700 transition hover:border-stone-400 hover:bg-stone-50 hover:text-stone-900">
                            {{ $category->name }}
                        </a>
                    @endforeach
                </div>
            </section>
        @endif

        {{-- Latest Notes Section --}}
        <section class="mt-14">
            <div class="mb-6 flex items-end justify-between">
                <div>
                    <h2 class="text-2xl font-extrabold tracking-tight text-stone-900">Catatan Terbaru</h2>
                    <p class="mt-0.5 text-xs text-stone-500">Jurnal dan tulisan teknis terbaru yang diterbitkan</p>
                </div>
                <a href="{{ route('posts.index') }}"
                    class="text-sm font-semibold text-stone-900 transition hover:text-stone-600">
                    Lihat semua &rarr;
                </a>
            </div>

            <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                @forelse ($latestPosts as $post)
                    <x-post-card :post="$post" />
                @empty
                    <div class="col-span-full rounded-xl border border-dashed border-stone-200 p-8 text-center">
                        <p class="text-sm text-stone-500">Belum ada artikel yang diterbitkan.</p>
                    </div>
                @endforelse
            </div>
        </section>

        {{-- Latest Projects Section --}}
        <section class="mt-16">
            <div class="mb-6 flex items-end justify-between">
                <div>
                    <h2 class="text-2xl font-extrabold tracking-tight text-stone-900">Proyek Terbaru</h2>
                    <p class="mt-0.5 text-xs text-stone-500">Showcase beberapa proyek & eksperimen yang dikerjakan</p>
                </div>
                <a href="{{ route('projects.index') }}"
                    class="text-sm font-semibold text-stone-900 transition hover:text-stone-600">
                    Lihat semua &rarr;
                </a>
            </div>

            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                @forelse ($latestProjects as $project)
                    <x-project-card :project="$project" />
                @empty
                    <div class="col-span-full rounded-xl border border-dashed border-stone-200 p-8 text-center">
                        <p class="text-sm text-stone-500">Belum ada proyek yang ditampilkan.</p>
                    </div>
                @endforelse
            </div>
        </section>



        {{-- About / Profile Callout --}}
        <section class="mt-16 rounded-2xl border border-stone-200/80 bg-white p-8 text-center shadow-sm sm:p-10">
            <h2 class="text-xl font-bold text-stone-900">Ingin tahu lebih banyak tentang saya?</h2>
            <p class="mx-auto mt-2 max-w-md font-serif text-sm leading-relaxed text-stone-600">
                Kenalan lebih jauh dengan latar belakang arsitektur sistem, tech stack yang sering saya gunakan, dan cerita
                di balik blog ini.
            </p>
            <a href="{{ route('profile.show') }}"
                class="mt-5 inline-block rounded-lg bg-stone-900 px-6 py-2.5 text-xs font-semibold text-white transition hover:bg-stone-700">
                Tentang Saya
            </a>
        </section>

    </div>
@endsection
