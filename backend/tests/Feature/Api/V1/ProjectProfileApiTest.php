<?php

namespace Tests\Feature\Api\V1;

use App\Models\Enums\ProjectStatus;

class ProjectProfileApiTest extends ApiTestCase
{
    public function test_projects_are_listed_with_pagination_and_skills(): void
    {
        $laravel = $this->makeSkill(['name' => 'Laravel']);

        foreach (range(1, 3) as $i) {
            $this->makeProject(['title' => "Project $i", 'started_at' => "2025-0$i-01"], [$laravel]);
        }

        $this->getJson('/api/v1/projects?per_page=2')
            ->assertOk()
            ->assertJsonStructure([
                'data' => [['id', 'title', 'slug', 'description', 'thumbnail', 'url', 'github_url', 'status', 'started_at', 'completed_at', 'skills']],
                'links',
                'meta' => ['current_page', 'per_page', 'total'],
            ])
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('meta.total', 3)
            ->assertJsonPath('data.0.title', 'Project 3')
            ->assertJsonPath('data.0.skills.0.slug', 'laravel')
            ->assertJsonMissingPath('data.0.content');
    }

    public function test_every_project_status_stays_public(): void
    {
        $this->makeProject(['title' => 'Ongoing', 'status' => ProjectStatus::Ongoing]);
        $this->makeProject(['title' => 'Completed', 'status' => ProjectStatus::Completed]);
        $this->makeProject(['title' => 'Archived', 'status' => ProjectStatus::Archived]);

        $this->getJson('/api/v1/projects')
            ->assertOk()
            ->assertJsonPath('meta.total', 3);

        $this->getJson('/api/v1/projects/archived')->assertOk()->assertJsonPath('data.status', 'archived');
    }

    public function test_project_detail_returns_full_details_and_skills(): void
    {
        $php = $this->makeSkill(['name' => 'PHP']);
        $this->makeProject(['title' => 'Cashier System'], [$php]);

        $base = rtrim(config('filesystems.disks.public.url'), '/');

        $this->getJson('/api/v1/projects/cashier-system')
            ->assertOk()
            ->assertJsonPath('data.title', 'Cashier System')
            ->assertJsonPath('data.content', '<p>Case study</p>')
            ->assertJsonPath('data.status', 'completed')
            ->assertJsonPath('data.started_at', '2025-01-15')
            ->assertJsonPath('data.completed_at', '2025-06-01')
            ->assertJsonPath('data.thumbnail', $base.'/projects/thumbnails/example.png')
            ->assertJsonPath('data.url', 'https://example.com')
            ->assertJsonPath('data.github_url', 'https://github.com/example/repo')
            ->assertJsonPath('data.skills.0.name', 'PHP');
    }

    public function test_unknown_project_returns_404(): void
    {
        $this->getJson('/api/v1/projects/nope')
            ->assertNotFound()
            ->assertJsonPath('message', 'Resource not found.');
    }

    public function test_profile_returns_public_data_and_skills_without_private_info(): void
    {
        $php = $this->makeSkill(['name' => 'PHP']);
        $this->makeProfile([$php]);

        $base = rtrim(config('filesystems.disks.public.url'), '/');

        $response = $this->getJson('/api/v1/profile');

        $response->assertOk()
            ->assertJsonPath('data.name', 'Asep Nurdin')
            ->assertJsonPath('data.headline', 'Software Engineer')
            ->assertJsonPath('data.bio_text', 'Hello there')
            ->assertJsonPath('data.avatar', $base.'/profiles/avatars/me.jpg')
            ->assertJsonPath('data.skills.0.slug', 'php')
            ->assertJsonMissingPath('data.email')
            ->assertJsonMissingPath('data.user')
            ->assertJsonMissingPath('data.password')
            ->assertJsonMissingPath('data.user_id');

        $this->assertStringNotContainsString('secret-admin@example.com', $response->getContent());
        $this->assertStringNotContainsString('password', strtolower($response->getContent()));
    }

    public function test_profile_returns_404_when_none_exists(): void
    {
        $this->getJson('/api/v1/profile')
            ->assertNotFound()
            ->assertJsonPath('message', 'Resource not found.');
    }
}
