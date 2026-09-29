<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class ErrorHandlingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Route::get('/_test_error/{code}', function (int $code) {
            abort($code);
        });
    }

    public function test_401_unauthorized_renders_custom_view(): void
    {
        $response = $this->get('/_test_error/401');

        $response->assertStatus(401);
        $response->assertSee('401');
        $response->assertSee('Sesi Masuk Diperlukan');
    }

    public function test_403_forbidden_renders_custom_view(): void
    {
        $response = $this->get('/_test_error/403');

        $response->assertStatus(403);
        $response->assertSee('403');
        $response->assertSee('Akses Ditolak');
    }

    public function test_404_not_found_renders_custom_view(): void
    {
        $response = $this->get('/halaman-acak-yang-pasti-tidak-ada-9988');

        $response->assertStatus(404);
        $response->assertSee('404');
        $response->assertSee('Halaman Tidak Ditemukan');
    }

    public function test_419_page_expired_renders_custom_view(): void
    {
        $response = $this->get('/_test_error/419');

        $response->assertStatus(419);
        $response->assertSee('419');
        $response->assertSee('Sesi Keamanan Berakhir');
    }

    public function test_429_too_many_requests_renders_custom_view(): void
    {
        $response = $this->get('/_test_error/429');

        $response->assertStatus(429);
        $response->assertSee('429');
        $response->assertSee('Batas Permintaan Tercapai');
    }

    public function test_500_server_error_renders_custom_view(): void
    {
        $response = $this->get('/_test_error/500');

        $response->assertStatus(500);
        $response->assertSee('500');
        $response->assertSee('Kendala Server Internal');
    }

    public function test_503_service_unavailable_renders_custom_view(): void
    {
        $response = $this->get('/_test_error/503');

        $response->assertStatus(503);
        $response->assertSee('503');
        $response->assertSee('Pemeliharaan Sistem');
    }

    public function test_json_request_returns_structured_json_error(): void
    {
        $response = $this->getJson('/_test_error/404');

        $response->assertStatus(404);
        $response->assertJson([
            'success' => false,
            'code' => 404,
            'message' => 'Sumber daya tidak ditemukan.',
        ]);
    }

    public function test_json_request_403_returns_structured_json_error(): void
    {
        $response = $this->getJson('/_test_error/403');

        $response->assertStatus(403);
        $response->assertJson([
            'success' => false,
            'code' => 403,
            'message' => 'Akses ditolak.',
        ]);
    }
}
