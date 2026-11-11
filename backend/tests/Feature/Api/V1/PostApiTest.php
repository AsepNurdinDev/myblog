<?php

namespace Tests\Feature\Api\V1;

use App\Models\Enums\CommentStatus;
use Illuminate\Support\Facades\DB;

class PostApiTest extends ApiTestCase
{
    public function test_published_posts_are_listed_and_drafts_are_hidden(): void
    {
        $this->makePost(['title' => 'Visible post']);
        $this->makeDraftPost(['title' => 'Hidden draft']);

        $response = $this->getJson('/api/v1/posts');

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.title', 'Visible post')
            ->assertJsonStructure([
                'data' => [['id', 'title', 'slug', 'excerpt', 'cover_image', 'published_at', 'category', 'tags']],
                'links' => ['first', 'last', 'prev', 'next'],
                'meta' => ['current_page', 'last_page', 'per_page', 'total'],
            ]);

        // Listings are summaries: no body, no comments.
        $response->assertJsonMissingPath('data.0.content');
        $response->assertJsonMissingPath('data.0.comments');
        $this->assertStringNotContainsString('Hidden draft', $response->getContent());
    }

    public function test_posts_are_sorted_by_latest_publication_date(): void
    {
        $this->makePost(['title' => 'Oldest', 'published_at' => now()->subDays(5)]);
        $this->makePost(['title' => 'Newest', 'published_at' => now()->subDay()]);
        $this->makePost(['title' => 'Middle', 'published_at' => now()->subDays(3)]);

        $this->getJson('/api/v1/posts')
            ->assertOk()
            ->assertJson(['data' => [
                ['title' => 'Newest'],
                ['title' => 'Middle'],
                ['title' => 'Oldest'],
            ]]);
    }

    public function test_pagination_and_per_page_work_and_are_capped_at_50(): void
    {
        foreach (range(1, 5) as $i) {
            $this->makePost(['title' => "Post $i", 'published_at' => now()->subDays($i)]);
        }

        $this->getJson('/api/v1/posts?per_page=2')
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('meta.per_page', 2)
            ->assertJsonPath('meta.total', 5)
            ->assertJsonPath('meta.last_page', 3);

        $this->getJson('/api/v1/posts?per_page=2&page=3')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('meta.current_page', 3);

        $this->getJson('/api/v1/posts?per_page=500')
            ->assertOk()
            ->assertJsonPath('meta.per_page', 50);

        // Garbage falls back to a sane value instead of failing.
        $this->getJson('/api/v1/posts?per_page=abc')
            ->assertOk()
            ->assertJsonPath('meta.per_page', 12);
    }

    public function test_search_matches_title_and_excerpt(): void
    {
        $this->makePost(['title' => 'Laravel tips', 'excerpt' => 'Something else']);
        $this->makePost(['title' => 'Plain post', 'excerpt' => 'Learning about Docker deployment']);
        $this->makePost(['title' => 'Unrelated', 'excerpt' => 'Nothing here']);
        $this->makeDraftPost(['title' => 'Laravel draft']);

        $this->getJson('/api/v1/posts?search=laravel')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.title', 'Laravel tips');

        $this->getJson('/api/v1/posts?search=docker')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.title', 'Plain post');

        $this->getJson('/api/v1/posts?search=zzzz')
            ->assertOk()
            ->assertJsonCount(0, 'data');
    }

    public function test_search_treats_like_wildcards_literally(): void
    {
        $this->makePost(['title' => 'Discount 100% real']);
        $this->makePost(['title' => 'Something else']);

        $this->getJson('/api/v1/posts?search=%25')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.title', 'Discount 100% real');
    }

    public function test_posts_can_be_filtered_by_category_and_tag(): void
    {
        $tutorial = $this->makeCategory(['name' => 'Tutorial']);
        $life = $this->makeCategory(['name' => 'Life']);
        $laravel = $this->makeTag(['name' => 'Laravel']);

        $this->makePost(['title' => 'A tutorial', 'category_id' => $tutorial->id], [$laravel]);
        $this->makePost(['title' => 'A life post', 'category_id' => $life->id]);

        $this->getJson('/api/v1/posts?category=tutorial')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.title', 'A tutorial')
            ->assertJsonPath('data.0.category.slug', 'tutorial');

        $this->getJson('/api/v1/posts?tag=laravel')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.title', 'A tutorial');
    }

    public function test_post_detail_returns_content_relationships_and_seo(): void
    {
        $category = $this->makeCategory(['name' => 'Tutorial']);
        $tag = $this->makeTag(['name' => 'Laravel']);
        $post = $this->makePost([
            'title' => 'Detailed post',
            'category_id' => $category->id,
            'seo_title' => 'Custom SEO title',
            'seo_description' => 'Custom SEO description',
        ], [$tag]);
        $this->makeComment($post, ['name' => 'Alice', 'content' => 'Approved one']);

        $response = $this->getJson('/api/v1/posts/detailed-post');

        $response->assertOk()
            ->assertJsonStructure(['data' => [
                'id', 'title', 'slug', 'excerpt', 'cover_image', 'content', 'reading_time',
                'published_at', 'seo' => ['title', 'description'],
                'category' => ['id', 'name', 'slug'],
                'tags' => [['id', 'name', 'slug']],
                'comments' => [['id', 'name', 'content', 'created_at']],
            ]])
            ->assertJsonPath('data.slug', 'detailed-post')
            ->assertJsonPath('data.category.slug', 'tutorial')
            ->assertJsonPath('data.tags.0.slug', 'laravel')
            ->assertJsonPath('data.seo.title', 'Custom SEO title')
            ->assertJsonPath('data.seo.description', 'Custom SEO description')
            ->assertJsonPath('data.comments.0.name', 'Alice')
            ->assertJsonCount(4, 'data.content')
            ->assertJsonPath('data.content.0.type', 'text')
            ->assertJsonPath('data.content.0.data.content', '<p>Hello world</p>')
            ->assertJsonPath('data.content.2.data.language', 'php')
            ->assertJsonPath('data.content.3.data.quote', 'Be kind');
    }

    public function test_seo_falls_back_to_title_and_excerpt(): void
    {
        $this->makePost(['title' => 'No seo', 'excerpt' => 'The excerpt', 'seo_title' => null, 'seo_description' => null]);

        $this->getJson('/api/v1/posts/no-seo')
            ->assertOk()
            ->assertJsonPath('data.seo.title', 'No seo')
            ->assertJsonPath('data.seo.description', 'The excerpt');
    }

    public function test_media_urls_are_absolute_and_use_the_public_disk(): void
    {
        $this->makePost(['title' => 'With media']);

        $response = $this->getJson('/api/v1/posts/with-media')->assertOk();

        $expectedBase = rtrim(config('filesystems.disks.public.url'), '/');

        $response->assertJsonPath('data.cover_image', $expectedBase.'/posts/covers/example.png')
            ->assertJsonPath('data.content.1.data.url', $expectedBase.'/posts/content/example.png')
            ->assertJsonPath('data.content.1.data.caption', 'A caption');

        $this->assertMatchesRegularExpression('#^https?://#', $response->json('data.cover_image'));
        // The raw storage path of the image block is not leaked as a separate key.
        $response->assertJsonMissingPath('data.content.1.data.image');
    }

    public function test_posts_without_a_cover_return_null(): void
    {
        $this->makePost(['title' => 'No cover', 'cover_image' => null]);

        $this->getJson('/api/v1/posts/no-cover')
            ->assertOk()
            ->assertJsonPath('data.cover_image', null);
    }

    public function test_missing_media_files_return_null_instead_of_a_broken_url(): void
    {
        $this->makePost([
            'title' => 'Missing cover',
            'cover_image' => 'posts/covers/not-found.png',
            'content' => [
                ['type' => 'image', 'data' => ['image' => 'posts/content/not-found.png']],
            ],
        ]);

        $this->getJson('/api/v1/posts/missing-cover')
            ->assertOk()
            ->assertJsonPath('data.cover_image', null)
            ->assertJsonPath('data.content.0.data.url', null);
    }

    public function test_only_approved_comments_are_returned_with_the_post(): void
    {
        $post = $this->makePost(['title' => 'Commented']);
        $this->makeComment($post, ['content' => 'approved-comment', 'status' => CommentStatus::Approved]);
        $this->makeComment($post, ['content' => 'pending-comment', 'status' => CommentStatus::Pending]);
        $this->makeComment($post, ['content' => 'rejected-comment', 'status' => CommentStatus::Rejected]);

        $response = $this->getJson('/api/v1/posts/commented')->assertOk();

        $response->assertJsonCount(1, 'data.comments')
            ->assertJsonPath('data.comments.0.content', 'approved-comment');
        $this->assertStringNotContainsString('pending-comment', $response->getContent());
        $this->assertStringNotContainsString('rejected-comment', $response->getContent());
    }

    public function test_post_detail_limits_embedded_comments_and_returns_total_count(): void
    {
        $post = $this->makePost(['title' => 'Many comments']);

        foreach (range(1, 55) as $number) {
            $this->makeComment($post, ['content' => 'Comment '.$number]);
        }

        $response = $this->getJson('/api/v1/posts/many-comments')->assertOk();

        $response->assertJsonCount(50, 'data.comments')
            ->assertJsonPath('data.comments_count', 55)
            ->assertJsonPath('data.comments.0.content', 'Comment 55')
            ->assertJsonPath('data.comments.49.content', 'Comment 6');
    }

    public function test_comment_email_addresses_are_never_exposed(): void
    {
        $post = $this->makePost(['title' => 'Privacy']);
        $this->makeComment($post, ['email' => 'private-person@example.org']);

        $detail = $this->getJson('/api/v1/posts/privacy')->assertOk();
        $detail->assertJsonMissingPath('data.comments.0.email');
        $this->assertStringNotContainsString('private-person@example.org', $detail->getContent());

        $list = $this->getJson('/api/v1/posts')->assertOk();
        $this->assertStringNotContainsString('private-person@example.org', $list->getContent());
    }

    public function test_draft_posts_return_404(): void
    {
        $this->makeDraftPost(['title' => 'Draft only']);

        $this->getJson('/api/v1/posts/draft-only')
            ->assertNotFound()
            ->assertExactJson(['message' => 'Resource not found.']);
    }

    public function test_unknown_post_returns_404(): void
    {
        $this->getJson('/api/v1/posts/does-not-exist')
            ->assertNotFound()
            ->assertJsonPath('message', 'Resource not found.');
    }

    public function test_listing_does_not_trigger_n_plus_one_queries(): void
    {
        $category = $this->makeCategory();
        $tag = $this->makeTag();

        foreach (range(1, 12) as $i) {
            $this->makePost(['title' => "Post $i", 'category_id' => $category->id], [$tag]);
        }

        DB::enableQueryLog();
        $this->getJson('/api/v1/posts')->assertOk();
        $queries = count(DB::getQueryLog());

        // count + posts + categories + tags(+pivot) regardless of the page size.
        $this->assertLessThanOrEqual(6, $queries);
    }
}
