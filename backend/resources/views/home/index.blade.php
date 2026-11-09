@extends('layouts.app')

@section('title', config('app.name', 'Personal Blog') . ' — Catatan, Journal & Project Portfolio')
@section('meta_description', $profile?->clean_bio ?? 'Ruang berbagi catatan teknis dan portfolio proyek.')

@section('content')
    <div class="mx-auto max-w-5xl px-4 py-8 sm:px-6 sm:py-16 font-sans">

        {{-- Hero / Personal Branding Section dengan Pop-up Foto --}}
        <section x-data="{ openAvatar: false }"
            class="flex flex-col items-start gap-6 border-b border-stone-200 pb-12 sm:flex-row sm:items-center">
            @if ($profile?->avatar)
                {{-- Tombol untuk membuka foto --}}
                <button type="button" @click="openAvatar = true"
                    class="group relative h-24 w-24 shrink-0 overflow-hidden rounded-full border border-stone-200 shadow-sm focus:outline-none focus:ring-2 focus:ring-stone-400 focus:ring-offset-2">
                    <img src="{{ Storage::disk('public')->url($profile->avatar) }}"
                        alt="{{ $profile->user->name ?? config('app.name') }}"
                        class="h-full w-full object-cover transition duration-300 group-hover:scale-105 group-hover:brightness-90">
                    <div
                        class="absolute inset-0 flex items-center justify-center bg-black/20 opacity-0 transition duration-300 group-hover:opacity-100">
                        <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v6m3-3H7" />
                        </svg>
                    </div>
                </button>

                {{-- Modal Lightbox (Pop-up Foto Profil) --}}
                <template x-teleport="body">
                    <div x-show="openAvatar" x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                        x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100"
                        x-transition:leave-end="opacity-0" @keydown.escape.window="openAvatar = false"
                        class="fixed inset-0 z-50 flex items-center justify-center bg-stone-900/80 p-4 backdrop-blur-sm"
                        style="display: none;">
                        {{-- Backdrop Overlay untuk Tutup saat Diklik di Luar --}}
                        <div class="fixed inset-0" @click="openAvatar = false"></div>

                        {{-- Konten Gambar Besar --}}
                        <div class="relative z-10 max-w-md overflow-hidden rounded-2xl bg-white p-2 shadow-2xl sm:max-w-lg">
                            <button @click="openAvatar = false"
                                class="absolute right-4 top-4 z-20 flex h-8 w-8 items-center justify-center rounded-full bg-stone-900/60 text-white backdrop-blur-md transition hover:bg-stone-900">
                                &times;
                            </button>

                            <img src="{{ Storage::disk('public')->url($profile->avatar) }}"
                                alt="{{ $profile->user->name ?? config('app.name') }}"
                                class="max-h-[80vh] w-full rounded-xl object-contain">
                        </div>
                    </div>
                </template>
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

                <p class="max-w-2xl font-serif text-base leading-relaxed text-stone-600">
                    {{ $profile?->clean_bio ?? 'Selamat datang di ruang digital saya. Tempat saya mendokumentasikan proses belajar, pengalaman membangun perangkat lunak, catatan arsitektur sistem, serta jurnal eksplorasi teknologi sehari-hari.' }}
                </p>
            </div>
        </section>

        {{-- Search Section --}}
        <section class="mt-8">
            <form action="{{ route('posts.index') }}" method="GET" class="relative max-w-xl">
                <div class="relative flex items-center">
                    <input type="text" name="search" placeholder="Cari artikel, topik, atau kata kunci..."
                        class="w-full rounded-xl border border-stone-200/90 bg-white py-2.5 pl-10 pr-20 text-xs sm:text-sm text-stone-800 placeholder-stone-400 shadow-sm transition focus:border-stone-400 focus:outline-none focus:ring-1 focus:ring-stone-400">
                    <div class="pointer-events-none absolute left-3 flex items-center text-stone-400">
                        <svg class="h-4 w-4 sm:h-5 sm:w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <button type="submit"
                        class="absolute right-1.5 rounded-lg bg-stone-900 px-3 py-1.5 text-xs font-semibold text-white hover:bg-stone-700">
                        Cari
                    </button>
                </div>
            </form>
        </section>

        {{-- Categories Section --}}
        @if ($categories->isNotEmpty())
            <section class="mt-10 sm:mt-12">
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

        {{-- Latest Notes Section --}}
        <section class="mt-12 sm:mt-14">
            <div class="mb-6 flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-extrabold tracking-tight text-stone-900 sm:text-2xl">Catatan Terbaru</h2>
                    <p class="text-xs text-stone-500">Jurnal dan tulisan teknis terbaru</p>
                </div>
                <a href="{{ route('posts.index') }}"
                    class="text-xs font-semibold text-stone-900 hover:text-stone-600 sm:text-sm">
                    Lihat semua &rarr;
                </a>
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 sm:gap-6 lg:grid-cols-3">
                @forelse ($latestPosts as $post)
                    <x-post-card :post="$post" />
                @empty
                    <div class="col-span-full rounded-xl border border-dashed border-stone-200 p-8 text-center">
                        <p class="text-sm text-stone-500">Belum ada artikel yang diterbitkan.</p>
                    </div>
                @endforelse
            </div>

            @if ($latestPosts->isNotEmpty())
                <div class="mt-8 text-center sm:hidden">
                    <a href="{{ route('posts.index') }}"
                        class="inline-block w-full rounded-xl border border-stone-200 bg-white py-2.5 text-xs font-semibold text-stone-800 shadow-sm active:bg-stone-50">
                        Lihat Semua Catatan &rarr;
                    </a>
                </div>
            @endif
        </section>

        {{-- Latest Projects Section --}}
        <section class="mt-12 sm:mt-16">
            <div class="mb-6 flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-extrabold tracking-tight text-stone-900 sm:text-2xl">Proyek Terbaru</h2>
                    <p class="text-xs text-stone-500">Eksperimen & proyek yang dikerjakan</p>
                </div>
                <a href="{{ route('projects.index') }}"
                    class="text-xs font-semibold text-stone-900 hover:text-stone-600 sm:text-sm">
                    Lihat semua &rarr;
                </a>
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 sm:gap-6">
                @forelse ($latestProjects as $project)
                    <x-project-card :project="$project" />
                @empty
                    <div class="col-span-full rounded-xl border border-dashed border-stone-200 p-8 text-center">
                        <p class="text-sm text-stone-500">Belum ada proyek yang ditampilkan.</p>
                    </div>
                @endforelse
            </div>
        </section>

        {{-- About Callout --}}
        <section class="mt-12 rounded-2xl border border-stone-200/80 bg-white p-6 text-center shadow-sm sm:mt-16 sm:p-10">
            <h2 class="text-lg font-bold text-stone-900 sm:text-xl">Ingin tahu lebih banyak tentang saya?</h2>
            <p class="mx-auto mt-2 max-w-md font-serif text-xs leading-relaxed text-stone-600 sm:text-sm">
                Mengenal latar belakang, tech stack, dan cerita di balik pembuatan blog ini.
            </p>
            <a href="{{ route('profile.show') }}"
                class="mt-4 inline-block rounded-lg bg-stone-900 px-5 py-2 text-xs font-semibold text-white hover:bg-stone-700">
                Tentang Saya
            </a>
        </section>

    </div>
@endsection
