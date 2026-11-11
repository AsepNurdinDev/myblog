@extends('layouts.app')

@section('title', '#' . $tag->name . ' — ' . config('app.name', 'Blog'))
@section('meta_description', 'Artikel dengan tag ' . $tag->name . '.')

@section('content')
    <div class="mx-auto max-w-3xl px-4 py-12 sm:px-6 sm:py-16">

        <header class="mb-10 space-y-2">
            <p class="text-xs font-semibold uppercase tracking-wide text-amber-700">Tag</p>
            <h1 class="text-2xl font-bold text-stone-900 sm:text-3xl">#{{ $tag->name }}</h1>
        </header>

        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
            @forelse ($posts as $post)
                <x-post-card :post="$post" />
            @empty
                <p class="col-span-full text-sm text-stone-500">Belum ada artikel yang diterbitkan dengan tag ini.</p>
            @endforelse
        </div>

        <div class="mt-10">
            {{ $posts->links() }}
        </div>

    </div>
@endsection
