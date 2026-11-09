@props(['profile' => null])

@php
    $navLinks = [
        ['route' => 'posts.index', 'label' => 'Catatan'],
        ['route' => 'projects.index', 'label' => 'Project'],
        ['route' => 'profile.show', 'label' => 'Tentang'],
    ];

    // Mengambil URL GitHub dari profile jika ada, atau menggunakan fallback default
    $githubUrl = $profile->github ?? 'https://github.com/asepnrdn';
@endphp

<header x-data="{ mobileOpen: false }" class="sticky top-0 z-40 border-b border-stone-200/80 bg-stone-50/90 backdrop-blur-md">
    <nav class="mx-auto flex h-16 max-w-5xl items-center justify-between px-4 sm:px-6" aria-label="Navigasi utama">
        {{-- Brand / App Name --}}
        <a href="{{ route('home') }}" class="text-lg font-bold tracking-tight text-stone-900 transition hover:text-stone-600">
            AsepBlog
        </a>

        {{-- Desktop Navigation & Search --}}
        <div class="hidden items-center gap-6 sm:flex">
            {{-- Quick Search (Desktop) --}}
            <form action="{{ route('posts.index') }}" method="GET" class="relative">
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Cari artikel..."
                    class="w-48 rounded-lg border border-stone-200/90 bg-white py-1.5 pl-8 pr-3 text-xs text-stone-800 placeholder-stone-400 shadow-sm transition-all focus:w-64 focus:border-stone-400 focus:outline-none focus:ring-1 focus:ring-stone-400"
                >
                <div class="pointer-events-none absolute left-2.5 top-1/2 -translate-y-1/2 text-stone-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
            </form>

            <div class="flex items-center gap-6 border-l border-stone-200/80 pl-6">
                @foreach ($navLinks as $link)
                    <a
                        href="{{ route($link['route']) }}"
                        class="text-sm font-medium transition {{ request()->routeIs($link['route']) ? 'font-semibold text-stone-900' : 'text-stone-500 hover:text-stone-900' }}"
                    >
                        {{ $link['label'] }}
                    </a>
                @endforeach

                {{-- GitHub Link --}}
                <a
                    href="{{ $githubUrl }}"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="text-stone-500 transition hover:text-stone-900"
                    aria-label="GitHub"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="h-5 w-5 fill-current" aria-hidden="true">
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.53 1.032 1.53 1.032.892 1.53 2.341 1.088 2.91.833.091-.647.35-1.088.636-1.339-2.221-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0 1 12 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.31.678.921.678 1.856 0 1.339-.012 2.419-.012 2.749 0 .268.18.58.688.482A10.02 10.02 0 0 0 22 12.017C22 6.484 17.522 2 12 2Z" />
                    </svg>
                </a>
            </div>
        </div>

        {{-- Mobile Hamburger Button --}}
        <button
            type="button"
            @click="mobileOpen = ! mobileOpen"
            class="inline-flex items-center justify-center rounded-lg p-2 text-stone-600 transition hover:bg-stone-200/50 hover:text-stone-900 focus:outline-none sm:hidden"
            :aria-expanded="mobileOpen"
            aria-label="Buka menu"
        >
            <svg x-show="! mobileOpen" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
            <svg x-show="mobileOpen" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" x-cloak>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </nav>

    {{-- Mobile Menu Container --}}
    <div
        x-show="mobileOpen"
        x-cloak
        @click.away="mobileOpen = false"
        x-transition:enter="transition ease-out duration-200 transform"
        x-transition:enter-start="opacity-0 -translate-y-2 scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
        x-transition:leave="transition ease-in duration-150 transform"
        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
        x-transition:leave-end="opacity-0 -translate-y-2 scale-95"
        class="fixed inset-x-0 top-16 z-50 border-b border-stone-200 bg-stone-50/95 p-4 shadow-lg backdrop-blur-lg sm:hidden"
    >
        <div class="space-y-3">
            {{-- Search Input (Mobile Hamburger Menu) --}}
            <form action="{{ route('posts.index') }}" method="GET" @submit="mobileOpen = false" class="relative">
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Cari artikel, topik, atau kata kunci..."
                    class="w-full rounded-xl border border-stone-200/90 bg-white py-2.5 pl-10 pr-4 text-sm text-stone-800 placeholder-stone-400 shadow-sm transition-all focus:border-stone-400 focus:outline-none focus:ring-1 focus:ring-stone-400"
                >
                <div class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-stone-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
            </form>

            <div class="space-y-1 border-t border-stone-200/60 pt-2">
                @foreach ($navLinks as $link)
                    <a
                        href="{{ route($link['route']) }}"
                        class="block rounded-lg px-4 py-2.5 text-base font-medium transition {{ request()->routeIs($link['route']) ? 'bg-stone-200/60 font-semibold text-stone-900' : 'text-stone-600 hover:bg-stone-200/40 hover:text-stone-900' }}"
                    >
                        {{ $link['label'] }}
                    </a>
                @endforeach

                {{-- GitHub Link (Mobile) --}}
                <a
                    href="{{ $githubUrl }}"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="flex items-center gap-2 rounded-lg px-4 py-2.5 text-base font-medium text-stone-600 transition hover:bg-stone-200/40 hover:text-stone-900"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="h-5 w-5 fill-current" aria-hidden="true">
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.53 1.032 1.53 1.032.892 1.53 2.341 1.088 2.91.833.091-.647.35-1.088.636-1.339-2.221-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0 1 12 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.31.678.921.678 1.856 0 1.339-.012 2.419-.012 2.749 0 .268.18.58.688.482A10.02 10.02 0 0 0 22 12.017C22 6.484 17.522 2 12 2Z" />
                    </svg>
                    GitHub
                </a>
            </div>
        </div>
    </div>
</header>