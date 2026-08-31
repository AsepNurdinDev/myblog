@props(['language' => null, 'code' => '', 'filename' => null])

@php
    // Map common/human-friendly language labels to the Prism.js language
    // identifiers we actually load in resources/js/app.js.
    $languageMap = [
        'php' => 'php',
        'javascript' => 'javascript',
        'js' => 'javascript',
        'typescript' => 'typescript',
        'ts' => 'typescript',
        'html' => 'markup',
        'markup' => 'markup',
        'css' => 'css',
        'bash' => 'bash',
        'shell' => 'bash',
        'sh' => 'bash',
        'go' => 'go',
        'golang' => 'go',
        'python' => 'python',
        'py' => 'python',
        'sql' => 'sql',
        'json' => 'json',
        'yaml' => 'yaml',
        'yml' => 'yaml',
        'dockerfile' => 'docker',
        'docker' => 'docker',
    ];

    $normalized = strtolower(trim((string) $language));
    $prismLanguage = $languageMap[$normalized] ?? 'none';
    $blockId = 'code-block-' . \Illuminate\Support\Str::random(8);
@endphp

<div class="not-prose my-6 overflow-hidden rounded-lg border border-stone-800 bg-stone-900 text-sm shadow-sm">
    <div class="flex items-center justify-between gap-4 border-b border-stone-700/80 bg-stone-800/60 px-4 py-2">
        <div class="flex min-w-0 items-center gap-2 text-xs text-stone-300">
            @if ($filename)
                <span class="truncate font-medium text-stone-100">{{ $filename }}</span>
                <span class="text-stone-600">&middot;</span>
            @endif
            <span class="uppercase tracking-wide text-stone-400">{{ $language ?: 'Text' }}</span>
        </div>

        <button
            type="button"
            data-copy-target="{{ $blockId }}"
            onclick="window.copyCodeBlock(this)"
            class="shrink-0 rounded-md px-2.5 py-1 text-xs font-medium text-stone-300 transition hover:bg-stone-700 hover:text-white"
        >
            <span data-copy-label>Copy</span>
        </button>
    </div>

    <pre class="overflow-x-auto p-4 leading-relaxed"><code id="{{ $blockId }}" class="language-{{ $prismLanguage }}">{{ $code }}</code></pre>
</div>
