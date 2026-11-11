<?php

namespace Tests\Feature\Api\V1;

use App\Models\Category;
use App\Models\Comment;
use App\Models\Enums\CommentStatus;
use App\Models\Enums\PostStatus;
use App\Models\Enums\ProjectStatus;
use App\Models\Post;
use App\Models\Profile;
use App\Models\Project;
use App\Models\Skill;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Shared helpers for the public API tests.
 *
 * The models have no factories, so data is created straight through the
 * Eloquent models (using their $fillable attributes).
 */
abstract class ApiTestCase extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Keep media tests isolated and make the standard fixture assets exist.
        // Keep media tests isolated and make the standard fixture assets exist.
        // Pass the public URL directly when constructing the fake disk.
        Storage::fake('public', [
            'url' => rtrim(config('app.url'), '/').'/storage',
        ]);

        foreach ([
            'posts/covers/example.png',
            'posts/content/example.png',
            'profiles/avatars/me.jpg',
            'projects/thumbnails/example.png',
        ] as $path) {
            Storage::disk('public')->put($path, 'test-fixture');
        }

        // Safety net: never run these tests against a real database.
        $this->assertSame(
            ':memory:',
            DB::connection()->getDatabaseName(),
            'API tests must run on the in-memory SQLite database.'
        );
    }

    protected function makeCategory(array $attributes = []): Category
    {
        $name = $attributes['name'] ?? 'Category '.Str::random(6);

        return Category::create([
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => 'About '.$name,
            ...$attributes,
        ]);
    }

    protected function makeTag(array $attributes = []): Tag
    {
        $name = $attributes['name'] ?? 'Tag '.Str::random(6);

        return Tag::create(['name' => $name, 'slug' => Str::slug($name), ...$attributes]);
    }

    protected function makeSkill(array $attributes = []): Skill
    {
        $name = $attributes['name'] ?? 'Skill '.Str::random(6);

        return Skill::create(['name' => $name, 'slug' => Str::slug($name), ...$attributes]);
    }

    protected function makePost(array $attributes = [], array $tags = []): Post
    {
        $title = $attributes['title'] ?? 'Post '.Str::random(8);

        $post = Post::create([
            'category_id' => null,
            'title' => $title,
            'slug' => Str::slug($title),
            'excerpt' => 'Excerpt for '.$title,
            'content' => [
                ['type' => 'text', 'data' => ['content' => '<p>Hello world</p>']],
                ['type' => 'image', 'data' => ['image' => 'posts/content/example.png', 'caption' => 'A caption']],
                ['type' => 'code', 'data' => ['language' => 'php', 'filename' => 'a.php', 'code' => '<?php echo 1;']],
                ['type' => 'quote', 'data' => ['quote' => 'Be kind', 'author' => 'Someone']],
            ],
            'cover_image' => 'posts/covers/example.png',
            'status' => PostStatus::Published,
            'published_at' => now()->subDay(),
            'seo_title' => 'SEO '.$title,
            'seo_description' => 'SEO description',
            ...$attributes,
        ]);

        if ($tags) {
            $post->tags()->attach(collect($tags)->map->id->all());
        }

        return $post;
    }

    protected function makeDraftPost(array $attributes = []): Post
    {
        return $this->makePost([
            'status' => PostStatus::Draft,
            'published_at' => null,
            ...$attributes,
        ]);
    }

    protected function makeComment(Post $post, array $attributes = []): Comment
    {
        return Comment::create([
            'post_id' => $post->id,
            'name' => 'Visitor',
            'email' => 'visitor@example.com',
            'content' => 'Nice article',
            'status' => CommentStatus::Approved,
            ...$attributes,
        ]);
    }

    protected function makeProject(array $attributes = [], array $skills = []): Project
    {
        $title = $attributes['title'] ?? 'Project '.Str::random(8);

        $project = Project::create([
            'title' => $title,
            'slug' => Str::slug($title),
            'description' => 'Description of '.$title,
            'content' => '<p>Case study</p>',
            'thumbnail' => 'projects/thumbnails/example.png',
            'url' => 'https://example.com',
            'github_url' => 'https://github.com/example/repo',
            'status' => ProjectStatus::Completed,
            'started_at' => '2025-01-15',
            'completed_at' => '2025-06-01',
            ...$attributes,
        ]);

        if ($skills) {
            $project->skills()->attach(collect($skills)->map->id->all());
        }

        return $project;
    }

    protected function makeProfile(array $skills = []): Profile
    {
        $user = User::factory()->create([
            'name' => 'Asep Nurdin',
            'email' => 'secret-admin@example.com',
        ]);

        $profile = Profile::create([
            'user_id' => $user->id,
            'avatar' => 'profiles/avatars/me.jpg',
            'headline' => 'Software Engineer',
            'bio' => '<p>Hello <strong>there</strong></p>',
            'location' => 'Bali',
            'github' => 'https://github.com/example',
        ]);

        if ($skills) {
            $profile->skills()->attach(collect($skills)->map->id->all());
        }

        return $profile;
    }
}
