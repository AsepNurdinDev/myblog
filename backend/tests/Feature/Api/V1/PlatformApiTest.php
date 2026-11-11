<?php

namespace Tests\Feature\Api\V1;

use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;

class PlatformApiTest extends ApiTestCase
{
    public function test_cors_allows_the_configured_frontend_origins(): void
    {
        foreach (['http://localhost:3000', 'https://asepblog.online'] as $origin) {
            $response = $this->call('OPTIONS', '/api/v1/posts', [], [], [], [
                'HTTP_ORIGIN' => $origin,
                'HTTP_ACCESS_CONTROL_REQUEST_METHOD' => 'GET',
            ]);

            $response->assertSuccessful();
            $this->assertSame($origin, $response->headers->get('Access-Control-Allow-Origin'));
            $this->assertNull($response->headers->get('Access-Control-Allow-Credentials'));
        }
    }

    public function test_cors_does_not_allow_other_origins(): void
    {
        $response = $this->call('GET', '/api/v1/categories', [], [], [], [
            'HTTP_ORIGIN' => 'https://evil.example',
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertNull($response->headers->get('Access-Control-Allow-Origin'));
    }

    public function test_unknown_api_routes_return_a_json_404(): void
    {
        $this->get('/api/v1/nothing-here', ['Accept' => 'application/json'])
            ->assertNotFound()
            ->assertJsonPath('message', 'Resource not found.');

        // Even without an Accept header, /api/* is always rendered as JSON.
        $this->get('/api/v1/nothing-here')
            ->assertNotFound()
            ->assertJsonPath('message', 'Resource not found.');
    }

    public function test_wrong_http_method_returns_405_json(): void
    {
        $this->postJson('/api/v1/posts')->assertStatus(405);
        $this->deleteJson('/api/v1/posts/anything')->assertStatus(405);
    }

    public function test_api_routes_need_no_session_or_csrf_token(): void
    {
        $this->makePost(['title' => 'No csrf']);

        $this->withoutMiddleware(PreventRequestForgery::class);

        $this->postJson('/api/v1/posts/no-csrf/comments', [
            'name' => 'A', 'email' => 'a@example.com', 'content' => 'Hello',
        ])->assertCreated();
    }

    public function test_no_administrator_management_routes_are_exposed(): void
    {
        $uris = collect(app('router')->getRoutes()->getRoutes())
            ->filter(fn ($route) => str_starts_with($route->uri(), 'api/'));

        $this->assertNotEmpty($uris);

        $writes = $uris->filter(fn ($r) => array_diff($r->methods(), ['GET', 'HEAD']))
            ->map(fn ($r) => implode('|', array_diff($r->methods(), ['HEAD'])).' '.$r->uri())
            ->values()->all();

        $this->assertSame(['POST api/v1/posts/{slug}/comments'], $writes);
    }
}
