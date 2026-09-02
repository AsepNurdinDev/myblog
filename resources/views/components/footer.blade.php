@props(['profile' => null])

@php
    // Fallback sosial media default jika profile belum diisi
    $socialLinks = [
        'GitHub' => $profile?->github ?? 'https://github.com/asepnrdn',
        'LinkedIn' => $profile?->linkedin ?? 'https://linkedin.com/in/asepnrdn',
        'Instagram' => $profile?->instagram ?? null,
        'Twitter' => $profile?->twitter ?? null,
        'Website' => $profile?->website ?? null,
    ];

    // Filter hanya link yang memiliki nilai
    $socialLinks = array_filter($socialLinks);

    $navLinks = [
        ['route' => 'home', 'label' => 'Beranda'],
        ['route' => 'posts.index', 'label' => 'Catatan'],
        ['route' => 'projects.index', 'label' => 'Project'],
        ['route' => 'profile.show', 'label' => 'Tentang'],
    ];
@endphp

<footer class="border-t border-stone-200/80 bg-stone-50">
    <div class="mx-auto max-w-4xl px-4 py-12 sm:px-6">
        <div class="grid grid-cols-1 gap-8 md:grid-cols-12">
            {{-- Deskripsi Ringkas / Branding --}}
            <div class="space-y-3 md:col-span-6">
                <a href="{{ route('home') }}" class="text-base font-bold tracking-tight text-stone-900 transition hover:text-stone-600">
                    AsepBlog
                </a>
                <p class="max-w-sm text-sm leading-relaxed text-stone-500">
                    Jurnal personal & ruang berbagi seputar pengembangan software, eksperimen teknologi, dan catatan harian.
                </p>
            </div>

            {{-- Navigasi Cepat --}}
            <div class="space-y-3 md:col-span-3">
                <p class="text-xs font-semibold uppercase tracking-wider text-stone-900">Navigasi</p>
                <ul class="space-y-2 text-sm">
                    @foreach ($navLinks as $link)
                        <li>
                            <a href="{{ route($link['route']) }}" class="text-stone-500 transition hover:text-stone-900">
                                {{ $link['label'] }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>

            {{-- Tautan Sosial Media --}}
            <div class="space-y-3 md:col-span-3">
                <p class="text-xs font-semibold uppercase tracking-wider text-stone-900">Terhubung</p>
                <ul class="space-y-2 text-sm">
                    @foreach ($socialLinks as $label => $url)
                        <li>
                            <a href="{{ $url }}" target="_blank" rel="noopener noreferrer" class="text-stone-500 transition hover:text-stone-900">
                                {{ $label }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>

        {{-- Copyright & Bottom Bar --}}
        <div class="mt-10 border-t border-stone-200/60 pt-6 text-center sm:flex sm:items-center sm:justify-between sm:text-left">
            <p class="text-xs text-stone-400">
                &copy; {{ now()->year }} AsepBlog. Semua hak cipta dilindungi.
            </p>
            <p class="mt-2 text-xs text-stone-400 sm:mt-0">
                Crafted with clean code & continuous learning.
            </p>
        </div>
    </div>
</footer>