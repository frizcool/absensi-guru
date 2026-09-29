<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic test example.
     */
    public function test_the_application_redirects_to_the_unified_login(): void
    {
        $response = $this->get('/');

        $response->assertRedirect('/sekolahku');
    }

    public function test_http_is_allowed_in_non_production_environment(): void
    {
        $response = $this->get('/sekolahku');
        $response->assertSuccessful();
        $response->assertHeaderMissing('Strict-Transport-Security');
    }

    public function test_https_is_enforced_in_production_environment(): void
    {
        $this->app->detectEnvironment(fn () => 'production');

        $response = $this->get('http://localhost/sekolahku');
        $response->assertRedirect('https://localhost/sekolahku');
    }

    public function test_route_login_resolves_to_unified_login(): void
    {
        $this->assertSame(route('school.login'), route('login'));
    }

    public function test_unauthenticated_requests_to_auth_routes_redirect_to_unified_login(): void
    {
        $response = $this->get('/filament-impersonate/leave');
        $response->assertRedirect('/sekolahku');
    }
}
