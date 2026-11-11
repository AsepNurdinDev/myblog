<?php

namespace App\Http\Resources\Api\V1;

use App\Models\Post;
use App\Support\Media;
use Illuminate\Http\Request;

/**
 * Full article representation: body blocks, SEO metadata and approved comments.
 *
 * Content blocks keep the Filament Builder structure ({type, data}); image
 * blocks get a ready-to-use absolute URL instead of the raw storage path.
 *
 * @mixin Post
 */
class PostDetailResource extends PostResource
{
    public function toArray(Request $request): array
    {
        $blocks = $this->normalizedBlocks();

        return [
            ...parent::toArray($request),
            'content' => $blocks,
            'reading_time' => $this->readingTime($blocks),
            'seo' => [
                'title' => $this->seo_title ?: $this->title,
                'description' => $this->seo_description ?: $this->excerpt,
            ],
            // The embedded preview is intentionally capped; clients can use
            // comments_count to show the total number of approved comments.
            'comments' => CommentResource::collection($this->whenLoaded('comments')),
            'comments_count' => (int) ($this->approved_comments_count ?? 0),
        ];
    }

    /**
     * @return array<int, array{type: string|null, data: array<string, mixed>}>
     */
    private function normalizedBlocks(): array
    {
        $blocks = [];

        foreach ($this->content ?? [] as $block) {
            if (! is_array($block)) {
                continue;
            }

            // Same tolerance as the Blade view: data may be nested or flat.
            $data = is_array($block['data'] ?? null) ? $block['data'] : $block;
            $type = $block['type'] ?? null;

            if ($type === 'image') {
                $data = [
                    'url' => Media::url($data['image'] ?? null),
                    'caption' => $data['caption'] ?? null,
                ];
            } elseif ($type === 'text') {
                $data = [
                    'content' => $data['content'] ?? ($data['text'] ?? ($data['body'] ?? null)),
                ];
            } else {
                unset($data['type']);
            }

            $blocks[] = ['type' => $type, 'data' => $data];
        }

        return $blocks;
    }

    /**
     * Estimated reading time in minutes (200 words per minute, minimum 1).
     */
    private function readingTime(array $blocks): int
    {
        $text = '';

        foreach ($blocks as $block) {
            if (in_array($block['type'], ['text', 'quote'], true)) {
                $text .= ' '.strip_tags((string) ($block['data']['content'] ?? $block['data']['quote'] ?? ''));
            }
        }

        return max(1, (int) ceil(str_word_count($text) / 200));
    }
}
