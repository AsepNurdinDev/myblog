<?php

namespace Tests\Feature\Api\V1;

use App\Models\Comment;
use App\Models\Enums\CommentStatus;

class CommentApiTest extends ApiTestCase
{
    private function payload(array $overrides = []): array
    {
        return [
            'name' => 'Jane Visitor',
            'email' => 'jane@example.com',
            'content' => 'Great article, thanks for sharing!',
            ...$overrides,
        ];
    }

    public function test_guest_can_submit_a_comment_without_authentication(): void
    {
        $post = $this->makePost(['title' => 'Open for comments']);

        $this->assertGuest();

        $response = $this->postJson('/api/v1/posts/open-for-comments/comments', $this->payload());

        $response->assertCreated()
            ->assertJsonPath('status', 'pending')
            ->assertJsonPath('data.name', 'Jane Visitor')
            ->assertJsonPath('data.content', 'Great article, thanks for sharing!')
            ->assertJsonStructure(['message', 'status', 'data' => ['id', 'name', 'content', 'created_at']]);

        $this->assertDatabaseCount('comments', 1);
        $this->assertGuest();
    }

    public function test_new_comments_are_stored_as_pending(): void
    {
        $post = $this->makePost(['title' => 'Moderated']);

        $this->postJson('/api/v1/posts/moderated/comments', $this->payload())->assertCreated();

        $comment = Comment::firstOrFail();
        $this->assertSame(CommentStatus::Pending, $comment->status);
        $this->assertSame($post->id, $comment->post_id);
        $this->assertSame('jane@example.com', $comment->email);
    }

    public function test_client_cannot_force_an_approved_status_or_another_post(): void
    {
        $post = $this->makePost(['title' => 'Target']);
        $other = $this->makePost(['title' => 'Other']);

        $this->postJson('/api/v1/posts/target/comments', $this->payload([
            'status' => 'approved',
            'post_id' => $other->id,
        ]))->assertCreated();

        $comment = Comment::firstOrFail();
        $this->assertSame(CommentStatus::Pending, $comment->status);
        $this->assertSame($post->id, $comment->post_id);
    }

    public function test_pending_comments_do_not_appear_publicly(): void
    {
        $this->makePost(['title' => 'Hidden until approved']);

        $this->postJson('/api/v1/posts/hidden-until-approved/comments', $this->payload())->assertCreated();

        $this->getJson('/api/v1/posts/hidden-until-approved')
            ->assertOk()
            ->assertJsonCount(0, 'data.comments');
    }

    public function test_the_submitted_email_is_never_returned(): void
    {
        $this->makePost(['title' => 'Private email']);

        $response = $this->postJson('/api/v1/posts/private-email/comments', $this->payload([
            'email' => 'very-private@example.net',
        ]))->assertCreated();

        $response->assertJsonMissingPath('data.email');
        $this->assertStringNotContainsString('very-private@example.net', $response->getContent());
    }

    public function test_invalid_data_returns_422_with_field_errors(): void
    {
        $this->makePost(['title' => 'Validated']);

        $this->postJson('/api/v1/posts/validated/comments', [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['name', 'email', 'content']);

        $this->postJson('/api/v1/posts/validated/comments', $this->payload(['email' => 'not-an-email']))
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['email']);

        $this->postJson('/api/v1/posts/validated/comments', $this->payload(['content' => str_repeat('a', 5001)]))
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['content']);

        $this->postJson('/api/v1/posts/validated/comments', $this->payload(['name' => str_repeat('n', 256)]))
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['name']);

        $this->assertDatabaseCount('comments', 0);
    }

    public function test_comments_with_too_many_links_are_rejected(): void
    {
        $this->makePost(['title' => 'Spammy']);

        $this->postJson('/api/v1/posts/spammy/comments', $this->payload([
            'content' => 'buy http://a.test http://b.test https://c.test www.d.test now',
        ]))->assertUnprocessable()->assertJsonValidationErrors(['content']);

        $this->postJson('/api/v1/posts/spammy/comments', $this->payload([
            'content' => 'See https://a.test and https://b.test please',
        ]))->assertCreated();
    }

    public function test_cannot_comment_on_an_unpublished_article(): void
    {
        $this->makeDraftPost(['title' => 'Still a draft']);

        $this->postJson('/api/v1/posts/still-a-draft/comments', $this->payload())
            ->assertNotFound();

        $this->assertDatabaseCount('comments', 0);
    }

    public function test_commenting_on_an_unknown_article_returns_404(): void
    {
        $this->postJson('/api/v1/posts/ghost/comments', $this->payload())
            ->assertNotFound()
            ->assertJsonPath('message', 'Resource not found.');
    }

    public function test_honeypot_submissions_look_successful_but_are_not_stored(): void
    {
        $this->makePost(['title' => 'Bot target']);

        $this->postJson('/api/v1/posts/bot-target/comments', $this->payload(['website' => 'http://spam.test']))
            ->assertCreated()
            ->assertJsonPath('data', null);

        $this->assertDatabaseCount('comments', 0);
    }

    public function test_identical_comments_sent_twice_are_stored_once(): void
    {
        $this->makePost(['title' => 'Double click']);

        $first = $this->postJson('/api/v1/posts/double-click/comments', $this->payload())->assertCreated();
        $second = $this->postJson('/api/v1/posts/double-click/comments', $this->payload())->assertCreated();

        $this->assertDatabaseCount('comments', 1);
        $this->assertSame($first->json('data.id'), $second->json('data.id'));
    }

    public function test_comment_submissions_are_rate_limited(): void
    {
        $this->makePost(['title' => 'Rate limited']);

        foreach (range(1, 5) as $i) {
            $this->postJson('/api/v1/posts/rate-limited/comments', $this->payload(['content' => "Comment number $i"]))
                ->assertCreated();
        }

        $this->postJson('/api/v1/posts/rate-limited/comments', $this->payload(['content' => 'Comment number 6']))
            ->assertStatus(429)
            ->assertJsonPath('message', 'Too many requests. Please try again later.')
            ->assertJsonStructure(['message', 'retry_after'])
            ->assertHeader('Retry-After');

        $this->assertDatabaseCount('comments', 5);
    }

    public function test_reading_endpoints_are_not_affected_by_the_comment_limit(): void
    {
        $this->makePost(['title' => 'Readable']);

        foreach (range(1, 8) as $i) {
            $this->getJson('/api/v1/posts/readable')->assertOk();
        }
    }
}
