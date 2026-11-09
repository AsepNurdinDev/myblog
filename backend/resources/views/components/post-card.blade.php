@props(['post'])

@php
    $rawContent = $post->content ?? $post->excerpt ?? '';

    if (is_array($rawContent)) {
        $rawContent = json_encode($rawContent);
    }

    $cleanContent = strip_tags((string) $rawContent);
    $wordCount = str_word_count($cleanContent);
    $readTime = max(1, ceil($wordCount / 200));
@endphp

<article class="group flex flex-col overflow-hidden rounded-2xl border border-stone-200/80 bg-white transition-all duration-300 hover:-translate-y-0.5 hover:border-stone-300 hover:shadow-lg hover:shadow-stone-200/50">
    {{-- Cover Image --}}
    @if ($post->cover_image)
        <a href="{{ route('posts.show', $post) }}" class="relative block aspect-[16/10] w-full overflow-hidden bg-stone-100">
            <img
                src="{{ Storage::disk('public')->url($post->cover_image) }}"
                alt="{{ $post->title }}"
                loading="lazy"
                class="h-full w-full object-cover transition-transform duration-500 ease-out group-hover:scale-105"
            >
            @if ($post->category)
                <span class="absolute left-3.5 top-3.5 rounded-full bg-stone-900/80 px-3 py-1 text-[11px] font-medium tracking-wide text-white backdrop-blur-md">
                    {{ $post->category->name }}
                </span>
            @endif
        </a>
    @endif

    <div class="flex flex-1 flex-col p-5 sm:p-6">
        {{-- Category Header (Jika tidak ada cover image) --}}
        @if (!$post->cover_image && $post->category)
            <div class="mb-3">
                <a href="{{ route('categories.show', $post->category) }}"
                    class="inline-block rounded-md bg-stone-100 px-2.5 py-1 text-xs font-semibold text-stone-700 transition hover:bg-stone-200">
                    {{ $post->category->name }}
                </a>
            </div>
        @endif

        {{-- Meta Info (Tanggal & Waktu Baca) --}}
        <div class="mb-2 flex items-center gap-2 text-xs text-stone-400">
            @if ($post->published_at)
                <time datetime="{{ $post->published_at->toDateString() }}">
                    {{ $post->published_at->translatedFormat('d M Y') }}
                </time>
                <span>&bull;</span>
            @endif
            <span>{{ $readTime }} min baca</span>
        </div>

        {{-- Title --}}
        <h3 class="text-base font-bold leading-snug tracking-tight text-stone-900 transition sm:text-lg group-hover:text-stone-600">
            <a href="{{ route('posts.show', $post) }}" class="focus:outline-none">
                {{ $post->title }}
            </a>
        </h3>

        {{-- Tags List Section --}}
        @if ($post->relationLoaded('tags') && $post->tags->isNotEmpty())
            <div class="mt-4 flex flex-wrap gap-1.5">
                @foreach ($post->tags as $tag)
                    <a href="{{ route('tags.show', $tag) }}" class="text-[11px] font-medium text-stone-400 transition hover:text-stone-700">
                        #{{ $tag->name }}
                    </a>
                @endforeach
            </div>
        @endif

        {{-- Footer Meta: Read More Action --}}
        <div class="mt-auto flex items-center justify-between border-t border-stone-100 pt-4">
            <a href="{{ route('posts.show', $post) }}"
                class="inline-flex items-center gap-1.5 text-xs font-semibold text-stone-900 transition hover:text-stone-600 group-hover:translate-x-0.5">
                Baca Artikel
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-3.5 w-3.5 transition-transform group-hover:translate-x-1">
                    <path fill-rule="evenodd" d="M3 10a.75.75 0 0 1 .75-.75h10.638l-3.96-4.158a.75.75 0 1 1 1.08-1.04l5.25 5.5a.75.75 0 0 1 0 1.04l-5.25 5.5a.75.75 0 1 1-1.08-1.04l3.96-4.158H3.75A.75.75 0 0 1 3 10Z" clip-rule="evenodd" />
                </svg>
            </a>
        </div>
    </div>
</article>