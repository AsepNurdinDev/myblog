@extends('layouts.app')

@section('title', 'Tentang — ' . config('app.name', 'Blog'))
@section('meta_description', $profile?->clean_bio ?: ($profile?->headline ?: 'Tentang saya.'))

@section('content')
    <div class="mx-auto max-w-3xl px-4 py-8 sm:px-6 sm:py-16 font-sans">

        {{-- Main Profile Card --}}
        <div 
            x-data="{ openAvatar: false }" 
            class="overflow-hidden rounded-2xl border border-stone-200/80 bg-white p-6 shadow-sm sm:p-10"
        >
            
            {{-- Header Profile --}}
            <header class="flex flex-col items-start gap-6 border-b border-stone-100 pb-8 sm:flex-row sm:items-center">
                @if ($profile?->avatar)
                    {{-- Tombol Foto Profil (Bisa Diklik) --}}
                    <button 
                        type="button" 
                        @click="openAvatar = true" 
                        class="group relative h-28 w-28 shrink-0 overflow-hidden rounded-full border border-stone-200 shadow-sm ring-4 ring-stone-100 focus:outline-none focus:ring-stone-400"
                    >
                        <img
                            src="{{ Storage::disk('public')->url($profile->avatar) }}"
                            alt="{{ $profile->user->name ?? 'Avatar' }}"
                            class="h-full w-full object-cover transition duration-300 group-hover:scale-105 group-hover:brightness-90"
                        >
                        <div class="absolute inset-0 flex items-center justify-center bg-black/20 opacity-0 transition duration-300 group-hover:opacity-100">
                            <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v6m3-3H7" />
                            </svg>
                        </div>
                    </button>

                    {{-- Pop-Up Lightbox Foto Profil --}}
                    <template x-teleport="body">
                        <div 
                            x-show="openAvatar" 
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0"
                            x-transition:enter-end="opacity-100"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100"
                            x-transition:leave-end="opacity-0"
                            @keydown.escape.window="openAvatar = false"
                            class="fixed inset-0 z-50 flex items-center justify-center bg-stone-900/80 p-4 backdrop-blur-sm"
                            style="display: none;"
                        >
                            <div class="fixed inset-0" @click="openAvatar = false"></div>

                            <div class="relative z-10 max-w-md overflow-hidden rounded-2xl bg-white p-2 shadow-2xl sm:max-w-lg">
                                <button 
                                    @click="openAvatar = false" 
                                    class="absolute right-4 top-4 z-20 flex h-8 w-8 items-center justify-center rounded-full bg-stone-900/60 text-white backdrop-blur-md transition hover:bg-stone-900"
                                >
                                    &times;
                                </button>
                                
                                <img 
                                    src="{{ Storage::disk('public')->url($profile->avatar) }}" 
                                    alt="{{ $profile->user->name ?? 'Avatar' }}"
                                    class="max-h-[80vh] w-full rounded-xl object-contain"
                                >
                            </div>
                        </div>
                    </template>
                @else
                    <div class="flex h-28 w-28 shrink-0 items-center justify-center rounded-full bg-stone-900 text-4xl font-bold text-white shadow-sm ring-4 ring-stone-100">
                        {{ Str::of($profile?->user?->name ?? config('app.name', 'A'))->substr(0, 1) }}
                    </div>
                @endif

                <div class="space-y-2">
                    <div class="flex items-center gap-2">
                        <span class="inline-block h-2.5 w-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span class="text-xs font-semibold uppercase tracking-wider text-stone-400">Software Engineer</span>
                    </div>

                    <h1 class="text-2xl font-extrabold tracking-tight text-stone-900 sm:text-3xl">
                        {{ $profile?->user?->name ?? 'Profile' }}
                    </h1>

                    @if ($profile?->headline)
                        <p class="text-sm font-semibold text-stone-700">{{ $profile->headline }}</p>
                    @endif

                    @if ($profile?->location)
                        <p class="flex items-center gap-1.5 text-xs font-medium text-stone-500">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-4 w-4 shrink-0 text-stone-400">
                                <path fill-rule="evenodd" d="M9.69 18.933c.185.16.398.29.63.386a1.5 1.5 0 0 0 1.36 0 3.06 3.06 0 0 0 .63-.386C13.6 17.634 17 14.24 17 9a7 7 0 1 0-14 0c0 5.24 3.4 8.634 6.69 9.933ZM10 11.5a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5Z" clip-rule="evenodd" />
                            </svg>
                            {{ $profile->location }}
                        </p>
                    @endif
                </div>
            </header>

            {{-- Bio Section --}}
            @if ($profile?->bio)
                <div class="py-8">
                    <h2 class="mb-4 text-xs font-bold uppercase tracking-wider text-stone-400">Tentang Saya</h2>
                    <div class="prose prose-stone max-w-none font-serif text-base leading-relaxed text-stone-700">
                        {!! $profile->bio !!}
                    </div>
                </div>
            @endif

            {{-- Rekomendasi Fitur Tambahan: Highlight / Core Focus --}}
            <div class="border-t border-stone-100 py-6">
                <h2 class="mb-4 text-xs font-bold uppercase tracking-wider text-stone-400">Fokus Utama</h2>
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                    <div class="rounded-xl border border-stone-200/60 bg-stone-50/50 p-4">
                        <p class="text-xs font-bold text-stone-900">Backend & API Architecture</p>
                        <p class="mt-1 text-xs text-stone-500">Membangun arsitektur server yang scalable, clean code, dan RESTful API.</p>
                    </div>
                    <div class="rounded-xl border border-stone-200/60 bg-stone-50/50 p-4">
                        <p class="text-xs font-bold text-stone-900">Modern Web Development</p>
                        <p class="mt-1 text-xs text-stone-500">Eksplorasi ekosistem Laravel, Blade, Tailwind CSS, dan integrasi UI responsif.</p>
                    </div>
                </div>
            </div>

            {{-- Skills Section --}}
            @if ($profile?->relationLoaded('skills') && $profile->skills->isNotEmpty())
                <div class="border-t border-stone-100 py-6">
                    <h2 class="mb-3 text-xs font-bold uppercase tracking-wider text-stone-400">Keahlian & Tech Stack</h2>
                    <div class="flex flex-wrap gap-2">
                        @foreach ($profile->skills as $skill)
                            <span class="rounded-lg border border-stone-200/80 bg-stone-100/70 px-3 py-1 text-xs font-semibold text-stone-700">
                                {{ $skill->name }}
                            </span>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Social Links --}}
            @php
                $socials = array_filter([
                    'github' => [
                        'label' => 'GitHub',
                        'url' => $profile?->github,
                        'icon' => 'M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.53 1.032 1.53 1.032.892 1.53 2.341 1.088 2.91.833.091-.647.35-1.088.636-1.339-2.221-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0 1 12 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.31.678.921.678 1.856 0 1.339-.012 2.419-.012 2.749 0 .268.18.58.688.482A10.02 10.02 0 0 0 22 12.017C22 6.484 17.522 2 12 2Z'
                    ],
                    'linkedin' => [
                        'label' => 'LinkedIn',
                        'url' => $profile?->linkedin,
                        'icon' => 'M19 3a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h14m-.5 15.5v-5.3a3.26 3.26 0 0 0-3.26-3.26c-.85 0-1.84.52-2.28 1.3v-1.11h-2.79v8.37h2.79v-4.93c0-.77.62-1.4 1.39-1.4a1.4 1.4 0 0 1 1.4 1.4v4.93h2.75M6.46 10.9v8.37H9.25V10.9H6.46M7.86 6.7a1.63 1.63 0 1 0 0 3.26 1.63 1.63 0 0 0 0-3.26Z'
                    ],
                    'instagram' => [
                        'label' => 'Instagram',
                        'url' => $profile?->instagram,
                        'icon' => 'M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z'
                    ],
                    'twitter' => [
                        'label' => 'Twitter / X',
                        'url' => $profile?->twitter,
                        'icon' => 'M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z'
                    ],
                    'website' => [
                        'label' => 'Website',
                        'url' => $profile?->website,
                        'icon' => 'M12 2a10 10 0 1 0 0 20 10 10 0 0 0 0-20zm6.93 6h-2.95a15.65 15.65 0 0 0-1.38-3.56A8.03 8.03 0 0 1 18.93 8zM12 4.04c.83 1.2 1.48 2.59 1.91 3.96h-3.82c.43-1.37 1.08-2.76 1.91-3.96zM4.07 14h2.95c.18 1.25.54 2.45 1.06 3.56A8.03 8.03 0 0 1 4.07 14zm2.95-4H4.07a8.03 8.03 0 0 1 1.01-3.56A15.65 15.65 0 0 0 7.02 10zm1.94 0h6.08c.18 1.28.18 2.72 0 4H8.96c-.18-1.28-.18-2.72 0-4zm1.08 6h3.92c-.43 1.37-1.08 2.76-1.91 3.96-.83-1.2-1.48-2.59-1.91-3.96zm4.98 3.56c.52-1.11.88-2.31 1.06-3.56h2.95a8.03 8.03 0 0 1-4.01 3.56z'
                    ],
                ]);
            @endphp

            @if (count($socials) > 0)
                <div class="border-t border-stone-100 py-6">
                    <h2 class="mb-3 text-xs font-bold uppercase tracking-wider text-stone-400">Tautan & Media Sosial</h2>
                    <div class="flex flex-wrap gap-2.5">
                        @foreach ($socials as $item)
                            <a
                                href="{{ $item['url'] }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="inline-flex items-center gap-2 rounded-xl border border-stone-200/90 bg-stone-50/50 px-3.5 py-2 text-xs font-semibold text-stone-700 transition-all hover:border-stone-400 hover:bg-stone-100 hover:text-stone-900 shadow-sm"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="h-4 w-4 fill-current">
                                    <path d="{{ $item['icon'] }}" />
                                </svg>
                                <span>{{ $item['label'] }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Rekomendasi Fitur Tambahan: Get in Touch / Direct Email CTA --}}
            <div class="mt-4 rounded-xl border border-stone-200/80 bg-stone-900 p-6 text-center text-white sm:flex sm:items-center sm:justify-between sm:text-left">
                <div>
                    <p class="text-sm font-bold">Tertarik berkolaborasi?</p>
                    <p class="mt-0.5 text-xs text-stone-400">Terbuka untuk diskusi proyek, konsultasi teknis, atau sekadar bertegur sapa.</p>
                </div>
                @if ($profile?->user?->email)
                    <a href="mailto:{{ $profile->user->email }}" class="mt-4 inline-block rounded-lg bg-white px-4 py-2 text-xs font-semibold text-stone-900 transition hover:bg-stone-200 sm:mt-0 shrink-0">
                        Kirim Email
                    </a>
                @endif
            </div>

        </div>

    </div>
@endsection