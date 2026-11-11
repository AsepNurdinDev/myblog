<?php

namespace Tests\Feature\Api\V1;

class HomeApiTest extends ApiTestCase
{
    public function test_home_returns_profile_latest_posts_projects_and_categories(): void
    {
        $php = $this->makeSkill(['name' => 'PHP']);
        $this->makeProfile([$php]);
        $category = $this->makeCategory(['name' => 'Tutorial']);
        $this->makeCategory(['name' => 'Journey']);

        foreach (range(1, 8) as $i) {
            $this->makePost(['title' => "Article $i", 'category_id' => $category->id, 'published_at' => now()->subDays(10 - $i)]);
        }
        $this->makeDraftPost(['title' => 'Secret draft']);

        foreach (range(1, 4) as $i) {
            $this->makeProject(['title' => "Project $i", 'started_at' => "2025-0$i-01"], [$php]);
        }

        $response = $this->getJson('/api/v1/home');

        $response->assertOk()
            ->assertJsonStructure(['data' => [
                'profile' => ['name', 'headline', 'bio', 'bio_text', 'location', 'avatar', 'skills'],
                'latest_posts' => [['id', 'title', 'slug', 'excerpt', 'cover_image', 'published_at', 'category', 'tags']],
                'latest_projects' => [['id', 'title', 'slug', 'status', 'skills']],
                'categories' => [['id', 'name', 'slug', 'posts_count']],
            ]])
            ->assertJsonCount(6, 'data.latest_posts')
            ->assertJsonCount(3, 'data.latest_projects')
            ->assertJsonCount(2, 'data.categories')
            ->assertJsonPath('data.profile.name', 'Asep Nurdin')
            ->assertJsonPath('data.latest_posts.0.title', 'Article 8')
            ->assertJsonPath('data.latest_projects.0.title', 'Project 4')
            // categories are ordered by name
            ->assertJsonPath('data.categories.0.name', 'Journey');

        $this->assertStringNotContainsString('Secret draft', $response->getContent());
        $this->assertStringNotContainsString('secret-admin@example.com', $response->getContent());
    }

    public function test_home_works_on_an_empty_database(): void
    {
        $this->getJson('/api/v1/home')
            ->assertOk()
            ->assertJsonPath('data.profile', null)
            ->assertJsonPath('data.latest_posts', [])
            ->assertJsonPath('data.latest_projects', [])
            ->assertJsonPath('data.categories', []);
    }
}
