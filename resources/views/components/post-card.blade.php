@props(['post'])

@php
    // Penanganan tipe data string/array untuk kalkulasi waktu baca secara aman
    $rawContent = $post->content ?? $post->excerpt ?? '';

    if (is_array($rawContent)) {
        $rawContent = json_encode($rawContent);
    }

    $cleanContent = strip_tags((string) $rawContent);
    $wordCount = str_word_count($cleanContent);
    $readTime = max(1, ceil($wordCount / 200));
@endphp

<article class="group flex flex-col overflow-hidden rounded-xl border border-stone-200/80 bg-white transition-all duration-300 hover:border-stone-300 hover:shadow-md">
    {{-- Cover Image --}}
    @if ($post->cover_image)
        <a href="{{ route('posts.show', $post) }}" class="block aspect-[16/9] w-full overflow-hidden bg-stone-100">
            <img
                src="{{ Storage::disk('public')->url($post->cover_image) }}"
                alt="{{ $post->title }}"
                loading="lazy"
                class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-[1.03]"
            >
        </a>
    @endif

    <div class="flex flex-1 flex-col p-6">
        {{-- Header Meta: Category & Read Time --}}
        <div class="mb-3 flex items-center justify-between gap-2 text-xs">
            @if ($post->category)
                <a
                    href="{{ route('categories.show', $post->category) }}"
                    class="rounded-md bg-stone-100 px-2.5 py-1 font-medium text-stone-700 transition hover:bg-stone-200 hover:text-stone-900"
                >
                    {{ $post->category->name }}
                </a>
            @else
                <span></span>
            @endif

            <span class="text-stone-400">
                {{ $readTime }} min baca
            </span>
        </div>

        {{-- Title --}}
        <h3 class="mb-2 text-xl font-bold leading-snug tracking-tight text-stone-900 transition group-hover:text-stone-600">
            <a href="{{ route('posts.show', $post) }}" class="focus:outline-none">
                {{ $post->title }}
            </a>
        </h3>

        {{-- Excerpt --}}
        @if ($post->excerpt)
            <p class="mb-4 line-clamp-3 text-sm leading-relaxed text-stone-600">
                {{ is_array($post->excerpt) ? implode(' ', $post->excerpt) : $post->excerpt }}
            </p>
        @endif

        {{-- Tags List Section --}}
        @if ($post->relationLoaded('tags') && $post->tags->isNotEmpty())
            <div class="mb-6 flex flex-wrap gap-1.5">
                @foreach ($post->tags as $tag)
                    <a
                        href="{{ route('tags.show', $tag) }}"
                        class="text-xs text-stone-400 transition hover:text-stone-700"
                    >
                        #{{ $tag->name }}
                    </a>
                @endforeach
            </div>
        @endif

        {{-- Footer Meta: Date & Read More --}}
        <div class="mt-auto flex items-center justify-between border-t border-stone-100 pt-4 text-xs font-medium text-stone-500">
            @if ($post->published_at)
                <time datetime="{{ $post->published_at->toDateString() }}" class="text-stone-400">
                    {{ $post->published_at->translatedFormat('d M Y') }}
                </time>
            @else
                <span></span>
            @endif

            <a
                href="{{ route('posts.show', $post) }}"
                class="inline-flex items-center gap-1 font-semibold text-stone-800 transition hover:text-stone-500 group-hover:translate-x-0.5"
            >
                Baca selengkapnya
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-4 w-4 transition-transform group-hover:translate-x-1">
                    <path fill-rule="evenodd" d="M3 10a.75.75 0 0 1 .75-.75h10.638l-3.96-4.158a.75.75 0 1 1 1.08-1.04l5.25 5.5a.75.75 0 0 1 0 1.04l-5.25 5.5a.75.75 0 1 1-1.08-1.04l3.96-4.158H3.75A.75.75 0 0 1 3 10Z" clip-rule="evenodd" />
                </svg>
            </a>
        </div>
    </div>
</article>