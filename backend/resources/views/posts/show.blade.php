@extends('layouts.app')

@section('title', $post->seo_title ?: $post->title)
@section('meta_description', $post->seo_description ?: ($post->excerpt ?: Str::limit(strip_tags($post->title), 155)))

@section('content')
    <article class="mx-auto max-w-5xl px-4 py-12 sm:px-6 sm:py-16 font-sans">

        {{-- Header Artikel --}}
        <header class="mx-auto max-w-3xl mb-10 text-center sm:text-left space-y-4">
            @if ($post->category)
                <a href="{{ route('categories.show', $post->category) }}"
                    class="inline-flex items-center gap-1.5 rounded-md bg-stone-100 px-2.5 py-1 text-xs font-semibold uppercase tracking-wider text-stone-700 transition hover:bg-stone-200">
                    <span class="h-1.5 w-1.5 rounded-full bg-stone-500"></span>
                    {{ $post->category->name }}
                </a>
            @endif

            <h1 class="text-3xl font-extrabold tracking-tight text-stone-900 sm:text-4xl lg:text-5xl lg:leading-tight">
                {{ $post->title }}
            </h1>

            @if ($post->excerpt)
                <p class="text-base font-normal leading-relaxed text-stone-600 sm:text-lg">
                    {{ $post->excerpt }}
                </p>
            @endif

            @if ($post->published_at)
                <div class="flex items-center gap-2 pt-2 text-xs font-medium text-stone-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-stone-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <time datetime="{{ $post->published_at->toDateString() }}">
                        {{ $post->published_at->translatedFormat('d F Y') }}
                    </time>
                </div>
            @endif
        </header>

        {{-- Cover Image --}}
        @if ($post->cover_image)
            <div class="mb-12 overflow-hidden rounded-2xl bg-stone-100 shadow-sm max-h-[520px]">
                <img src="{{ Storage::disk('public')->url($post->cover_image) }}" alt="{{ $post->title }}"
                    class="h-full w-full object-cover">
            </div>
        @endif

        {{-- Structured Content Section --}}
        <div class="mx-auto max-w-4xl">
            <div class="article-content prose prose-stone max-w-none font-serif text-base leading-relaxed text-stone-800 lg:prose-lg">
                @forelse ($post->content ?? [] as $block)
                    @php
                        $blockData = $block['data'] ?? $block;
                    @endphp
                    @switch($block['type'] ?? null)
                        @case('text')
                            @php
                                $html = $blockData['content'] ?? ($blockData['text'] ?? ($blockData['body'] ?? null));
                            @endphp
                            @if ($html)
                                <div class="prose-block">{!! $html !!}</div>
                            @endif
                        @break

                        @case('image')
                            @if (!empty($blockData['image']))
                                <figure class="not-prose my-10">
                                    <img src="{{ Storage::disk('public')->url($blockData['image']) }}" alt="{{ $blockData['caption'] ?? $post->title }}"
                                        loading="lazy" class="w-full rounded-xl border border-stone-200/80 shadow-sm">
                                    @if (!empty($blockData['caption']))
                                        <figcaption class="mt-2.5 text-center text-xs text-stone-500 font-sans">
                                            {{ $blockData['caption'] }}
                                        </figcaption>
                                    @endif
                                </figure>
                            @endif
                        @break

                        @case('code')
                            <x-code-block :language="$blockData['language'] ?? null" :code="$blockData['code'] ?? ''" :filename="$blockData['filename'] ?? null" />
                        @break

                        @case('quote')
                            @if (!empty($blockData['quote']))
                                <blockquote class="not-prose my-10 border-l-4 border-stone-800 bg-stone-50 px-6 py-5 rounded-r-xl">
                                    <p class="font-serif text-lg italic leading-relaxed text-stone-800">
                                        &ldquo;{{ $blockData['quote'] }}&rdquo;
                                    </p>
                                    @if (!empty($blockData['author']))
                                        <footer class="mt-3 font-sans text-xs font-semibold uppercase tracking-wider text-stone-500">
                                            &mdash; {{ $blockData['author'] }}
                                        </footer>
                                    @endif
                                </blockquote>
                            @endif
                        @break
                    @endswitch
                @empty
                    <p class="text-stone-500 italic">Artikel ini belum memiliki konten.</p>
                @endforelse
            </div>

            {{-- Tags --}}
            @if ($post->tags->isNotEmpty())
                <div class="mt-12 flex flex-wrap gap-2 border-t border-stone-200/80 pt-6">
                    @foreach ($post->tags as $tag)
                        <span class="rounded-lg bg-stone-100 px-3 py-1 text-xs font-semibold text-stone-600">
                            #{{ $tag->name }}
                        </span>
                    @endforeach
                </div>
            @endif

            {{-- Comments Section --}}
            <section id="comments" class="mt-16 border-t border-stone-200/80 pt-10 font-sans">
                <div class="flex items-center justify-between">
                    <h2 class="text-2xl font-bold tracking-tight text-stone-900">
                        Komentar <span class="text-sm font-semibold text-stone-400">({{ $post->comments->count() }})</span>
                    </h2>
                </div>

                @if (session('status') === 'comment-submitted')
                    <div class="mt-4 rounded-xl border border-emerald-200 bg-emerald-50/80 p-4 text-xs font-medium text-emerald-800">
                        Terima kasih! Komentar Anda telah dikirim dan akan tampil setelah disetujui.
                    </div>
                @endif

                {{-- Comments List --}}
                <div class="mt-8 space-y-4">
                    @forelse ($post->comments as $comment)
                        <div class="rounded-xl border border-stone-200/80 bg-white p-5 shadow-sm">
                            <div class="flex items-center justify-between border-b border-stone-100 pb-3">
                                <p class="text-sm font-bold text-stone-900">{{ $comment->name }}</p>
                                <time class="text-xs text-stone-400" datetime="{{ $comment->created_at->toDateString() }}">
                                    {{ $comment->created_at->translatedFormat('d M Y') }}
                                </time>
                            </div>
                            <p class="mt-3 text-sm leading-relaxed text-stone-700">{{ $comment->content }}</p>
                        </div>
                    @empty
                        <div class="rounded-xl border border-dashed border-stone-200/90 bg-stone-50/50 p-6 text-center">
                            <p class="text-xs text-stone-500">Belum ada komentar. Jadilah yang pertama berkomentar!</p>
                        </div>
                    @endforelse
                </div>

                {{-- Comment Form --}}
                <div class="mt-12 rounded-2xl border border-stone-200/80 bg-white p-6 shadow-sm sm:p-8">
                    <h3 class="text-base font-bold text-stone-900 mb-4">Tinggalkan Komentar</h3>
                    <form method="POST" action="{{ route('comments.store', $post) }}" class="space-y-4">
                        @csrf

                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <label for="name" class="block text-xs font-semibold text-stone-700">Nama</label>
                                <input type="text" name="name" id="name" value="{{ old('name') }}" required
                                    placeholder="Nama Anda"
                                    class="mt-1 block w-full rounded-xl border border-stone-200/90 px-3.5 py-2.5 text-sm text-stone-800 placeholder-stone-400 shadow-sm focus:border-stone-400 focus:outline-none focus:ring-1 focus:ring-stone-400">
                                @error('name')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="email" class="block text-xs font-semibold text-stone-700">Email</label>
                                <input type="email" name="email" id="email" value="{{ old('email') }}" required
                                    placeholder="email@domain.com"
                                    class="mt-1 block w-full rounded-xl border border-stone-200/90 px-3.5 py-2.5 text-sm text-stone-800 placeholder-stone-400 shadow-sm focus:border-stone-400 focus:outline-none focus:ring-1 focus:ring-stone-400">
                                <p class="mt-1 text-[11px] text-stone-400">Email tidak akan ditampilkan ke publik.</p>
                                @error('email')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label for="content" class="block text-xs font-semibold text-stone-700">Komentar</label>
                            <textarea name="content" id="content" rows="4" required
                                placeholder="Tulis komentar atau tanggapan Anda di sini..."
                                class="mt-1 block w-full rounded-xl border border-stone-200/90 px-3.5 py-2.5 text-sm text-stone-800 placeholder-stone-400 shadow-sm focus:border-stone-400 focus:outline-none focus:ring-1 focus:ring-stone-400">{{ old('content') }}</textarea>
                            @error('content')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <button type="submit"
                            class="inline-flex items-center rounded-xl bg-stone-900 px-6 py-2.5 text-xs font-semibold text-white transition hover:bg-stone-700 shadow-sm">
                            Kirim Komentar
                        </button>
                    </form>
                </div>
            </section>

        </div>

    </article>
@endsection