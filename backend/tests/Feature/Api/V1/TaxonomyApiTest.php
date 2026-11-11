<?php

namespace Tests\Feature\Api\V1;

class TaxonomyApiTest extends ApiTestCase
{
    public function test_categories_are_listed_with_published_post_counts(): void
    {
        $tutorial = $this->makeCategory(['name' => 'Tutorial']);
        $this->makeCategory(['name' => 'Empty']);
        $this->makePost(['title' => 'One', 'category_id' => $tutorial->id]);
        $this->makePost(['title' => 'Two', 'category_id' => $tutorial->id]);
        $this->makeDraftPost(['title' => 'Three', 'category_id' => $tutorial->id]);

        $this->getJson('/api/v1/categories')
            ->assertOk()
            ->assertJsonStructure(['data' => [['id', 'name', 'slug', 'description', 'posts_count']]])
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('data.0.name', 'Empty')
            ->assertJsonPath('data.0.posts_count', 0)
            ->assertJsonPath('data.1.slug', 'tutorial')
            ->assertJsonPath('data.1.posts_count', 2);
    }

    public function test_category_detail_returns_category_and_paginated_published_posts(): void
    {
        $tutorial = $this->makeCategory(['name' => 'Tutorial']);
        $life = $this->makeCategory(['name' => 'Life']);

        foreach (range(1, 3) as $i) {
            $this->makePost(['title' => "Tutorial $i", 'category_id' => $tutorial->id]);
        }
        $this->makeDraftPost(['title' => 'Tutorial draft', 'category_id' => $tutorial->id]);
        $this->makePost(['title' => 'Life post', 'category_id' => $life->id]);

        $response = $this->getJson('/api/v1/categories/tutorial?per_page=2');

        $response->assertOk()
            ->assertJsonPath('category.slug', 'tutorial')
            ->assertJsonPath('category.name', 'Tutorial')
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('meta.total', 3)
            ->assertJsonPath('meta.last_page', 2)
            ->assertJsonStructure(['data' => [['id', 'title', 'slug']], 'links', 'meta', 'category']);

        $this->assertStringNotContainsString('Tutorial draft', $response->getContent());
        $this->assertStringNotContainsString('Life post', $response->getContent());
    }

    public function test_unknown_category_returns_404(): void
    {
        $this->getJson('/api/v1/categories/nope')
            ->assertNotFound()
            ->assertJsonPath('message', 'Resource not found.');
    }

    public function test_tags_are_listed_with_published_post_counts(): void
    {
        $laravel = $this->makeTag(['name' => 'Laravel']);
        $this->makeTag(['name' => 'Docker']);
        $this->makePost(['title' => 'Published'], [$laravel]);
        $this->makeDraftPost(['title' => 'Unpublished']);

        $this->getJson('/api/v1/tags')
            ->assertOk()
            ->assertJsonStructure(['data' => [['id', 'name', 'slug', 'posts_count']]])
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('data.0.name', 'Docker')
            ->assertJsonPath('data.1.slug', 'laravel')
            ->assertJsonPath('data.1.posts_count', 1);
    }

    public function test_tag_detail_returns_tag_and_paginated_published_posts(): void
    {
        $laravel = $this->makeTag(['name' => 'Laravel']);
        $docker = $this->makeTag(['name' => 'Docker']);

        foreach (range(1, 3) as $i) {
            $this->makePost(['title' => "Laravel $i"], [$laravel]);
        }
        $draft = $this->makeDraftPost(['title' => 'Laravel draft']);
        $draft->tags()->attach($laravel->id);
        $this->makePost(['title' => 'Docker post'], [$docker]);

        $response = $this->getJson('/api/v1/tags/laravel?per_page=2');

        $response->assertOk()
            ->assertJsonPath('tag.slug', 'laravel')
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('meta.total', 3)
            ->assertJsonStructure(['data' => [['id', 'title', 'slug', 'tags']], 'links', 'meta', 'tag']);

        $this->assertStringNotContainsString('Laravel draft', $response->getContent());
        $this->assertStringNotContainsString('Docker post', $response->getContent());
    }

    public function test_unknown_tag_returns_404(): void
    {
        $this->getJson('/api/v1/tags/nope')
            ->assertNotFound()
            ->assertJsonPath('message', 'Resource not found.');
    }
}
